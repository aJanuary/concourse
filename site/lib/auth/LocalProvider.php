<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/mail.php');
require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(__DIR__ . '/AuthProvider.php');
require_once(__DIR__ . '/InlineLoginForm.php');
require_once(__DIR__ . '/MemberSource.php');
require_once(__DIR__ . '/login_throttle.php');

// Logs in with an email address, then either a password or a six digit code
// sent to that address.
//
// Forms POST to /login?provider=local with a `phase`; each phase either
// returns an Identity or redirects to a GET page that shows the next step. The
// chooser page and the `selected-member` phase are reached with a signed
// token, which must verify before anything is read from it.
//
// An email with a password is asked for it first, with the code as an
// alternative; any other email is sent a code straight away. An unknown email
// is sent a message saying no member was found, and sees the same pages as a
// member without a password.
class LocalProvider implements AuthProvider, InlineLoginForm {
  const CODE_TTL = 60 * 15;
  const CHOOSER_TTL = 60 * 5;
  const MAX_CODE_FAILURES = 5;
  const MIN_PASSWORD_LENGTH = 8;

  const ERRORS = [
    'invalid-email' => 'Please enter a valid email address.',
    'invalid-code' => 'The code you entered is invalid or has expired. Please try again or send a new code.',
    'incorrect-password' => 'The email address or password you entered is incorrect. Please try again.',
    'too-many-attempts' => 'There have been too many attempts for this email address or network. Please wait a few minutes and try again.',
    'no-member' => 'No member found with this email address.',
    'link-expired' => 'Your login has expired. Please try logging in again.',
    'link-invalid' => 'Invalid login link. Please try logging in again.',
  ];

  private string $redirect = '/';

  public function __construct(private ?MemberSource $members = null) {
    $this->members ??= auth_member_source();
  }

  public function id(): string {
    return 'local';
  }

  public function display_name(): string {
    return 'Log in with email';
  }

  // Every page is a GET, so a refresh never resubmits a form. State travels in
  // the query string: the email, the redirect, an error code, and for the
  // chooser a signed token.
  public function begin(string $redirect): void {
    $this->redirect = $this->normalize_redirect($redirect);
    $error = $_GET['error'] ?? null;
    $error = isset(self::ERRORS[$error]) ? $error : null;
    $email = $this->normalize_email((string) ($_GET['email'] ?? ''));

    switch ($_GET['phase'] ?? '') {
      case 'options':
        if ($email !== null) {
          $this->render_options($email, $error);
        }
        break;

      case 'enter-code':
        if ($email !== null) {
          $this->render_code_form($email, $error);
        }
        break;

      case 'choose':
        $this->render_chooser((string) ($_GET['token'] ?? ''));
    }

    $this->render_entry($error);
  }

  public function inline_form(string $redirect): Template {
    return new Template('auth/local/inline-form.html.twig', ['redirect' => $redirect]);
  }

  // Each phase either finishes the login or redirects to the next page
  public function complete(array $params): Identity {
    $phase = $params['phase'] ?? '';
    $this->redirect = $this->normalize_redirect((string) ($params['redirect'] ?? '/'));
    $redirect = $this->redirect;
    $email = $this->normalize_email((string) ($params['email'] ?? ''));

    if ($phase === 'selected-member') {
      return $this->complete_selected_member($params, $redirect);
    }

    if ($email === null) {
      $this->go(null, null, $redirect, 'invalid-email');
    }

    switch ($phase) {
      case 'entered-email':
        if (db_email_has_password_auth($email)) {
          $this->go('options', $email, $redirect);
        }
        $this->request_code($email, $redirect);

      case 'requested-code':
        $this->request_code($email, $redirect);

      case 'entered-code':
        if (login_throttle_blocked('code-verify', $email)) {
          $this->go('enter-code', $email, $redirect, 'too-many-attempts');
        }
        $code = preg_replace('/\D/', '', (string) ($params['code'] ?? ''));
        if (!db_consume_login_code($email, $this->hash_code($code), self::MAX_CODE_FAILURES)) {
          login_throttle_hit('code-verify', $email);
          $this->go('enter-code', $email, $redirect, 'invalid-code');
        }
        return $this->identity_for_verified_email($email, $redirect);

      case 'entered-password':
        if (login_throttle_blocked('password', $email)) {
          $this->go('options', $email, $redirect, 'too-many-attempts');
        }
        if (!db_check_password_auth($email, (string) ($params['password'] ?? ''))) {
          login_throttle_hit('password', $email);
          $this->go('options', $email, $redirect, 'incorrect-password');
        }
        return $this->identity_for_verified_email($email, $redirect);
    }

    throw new AuthFailure('unknown-phase');
  }

  // $phase null shows the email entry page
  private function go(?string $phase, ?string $email, string $redirect, ?string $error = null, ?string $token = null): never {
    $query = ['provider' => 'local', 'redirect' => $redirect];
    foreach (['phase' => $phase, 'email' => $email, 'error' => $error, 'token' => $token] as $key => $value) {
      if ($value !== null) {
        $query[$key] = $value;
      }
    }
    header('Location: /login?' . http_build_query($query));
    exit;
  }

  private function request_code(string $email, string $redirect): never {
    $phase = db_email_has_password_auth($email) ? 'options' : 'enter-code';
    if (login_throttle_blocked('code-request', $email)) {
      $this->go($phase, $email, $redirect, 'too-many-attempts');
    }
    login_throttle_hit('code-request', $email);
    $this->send_login_code($email);
    $this->go('enter-code', $email, $redirect);
  }

  // For an email the caller has verified control of. Returns the member's
  // identity, or shows a chooser and exits when the email has several members.
  public function identity_for_verified_email(string $email, string $redirect): Identity {
    $members = $this->members->members_for_email($email);

    if (count($members) === 0) {
      $this->go(null, null, $redirect, 'no-member');
    }
    if (count($members) === 1) {
      return $this->identity_for_member($members[0]);
    }

    $token = create_secure_token('/login', ['phase' => 'select-member', 'email' => $email], self::CHOOSER_TTL);
    $this->go('choose', null, $redirect, null, $token);
  }

  private function render_chooser(string $token): never {
    $data = verify_secure_link('/login', $token);
    if ($data === 'expired') {
      $this->render_entry('link-expired');
    }
    if ($data === 'invalid' || ($data['phase'] ?? '') !== 'select-member') {
      $this->render_entry('link-invalid');
    }

    $this->show_page('choose', [
      'email' => $data['email'],
      'members' => $this->members->members_for_email($data['email']),
      'token' => $token,
    ]);
  }

  public function send_login_code(string $email): void {
    if (count($this->members->members_for_email($email)) === 0) {
      $this->send_no_member_email($email, CON_SHORT_NAME . ' login code');
      return;
    }

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    db_create_login_code($email, $this->hash_code($code), time() + self::CODE_TTL);

    send_email($email, $code . ' is your ' . CON_SHORT_NAME . ' login code', 'Your member portal login code', [
      ['type' => 'para', 'text' => 'Your login code is:'],
      ['type' => 'code', 'text' => $code],
      ['type' => 'para', 'text' => "This code will expire in 15 minutes.\n\nIf you did not request this code, you can safely ignore this email."],
    ]);
  }

  // $kind is 'set' or 'reset'. The link goes to the page of the same name.
  public function send_password_email(string $email, string $redirect, string $kind): void {
    $task_start = $kind === 'set' ? 'Set your password' : 'Reset your password';
    $task_inline = $kind === 'set' ? 'set your password' : 'reset your password';
    $subject = $kind === 'set' ? 'Set your password for ' . CON_SHORT_NAME : CON_SHORT_NAME . ' password reset';

    if (count($this->members->members_for_email($email)) === 0) {
      $this->send_no_member_email($email, $subject, $task_inline);
      return;
    }

    $path = $kind === 'set' ? '/set-password' : '/forgot-password';
    $link = ROOT_URL . create_secure_link($path, ['email' => $email, 'redirect' => $redirect]);

    send_email($email, $subject, $task_start, [
      ['type' => 'para', 'text' => 'Click the button below to ' . $task_inline . '.'],
      ['type' => 'button', 'href' => $link, 'text' => $task_start],
      ['type' => 'para', 'text' => "This link will expire in 15 minutes.\n\nIf you did not request this link, you can safely ignore this email."],
    ]);
  }

  private function send_no_member_email(string $email, string $subject, string $task = 'log in'): void {
    send_email($email, $subject, 'No member found', [
      ['type' => 'para', 'text' => 'Hello,'],
      ['type' => 'para', 'text' => 'We received a request to ' . $task . ', but no member is registered with this email address.'],
      ['type' => 'para', 'text' => 'If you would like to, you can [buy a membership](' . REGISTRATION_URL . ').'],
      ['type' => 'para', 'text' => 'If you believe this is an error, and you should have a membership, please email [' . REGISTRATION_EMAIL . '](mailto:' . REGISTRATION_EMAIL . ').'],
      ['type' => 'para', 'text' => "Best regards,\n\nThe Convention Team"],
    ]);
  }

  private function complete_selected_member(array $params, string $redirect): Identity {
    $data = verify_secure_link('/login', (string) ($params['token'] ?? ''));
    if ($data === 'expired') {
      $this->go(null, null, $redirect, 'link-expired');
    }
    if ($data === 'invalid' || ($data['phase'] ?? '') !== 'select-member') {
      $this->go(null, null, $redirect, 'link-invalid');
    }

    foreach ($this->members->members_for_email($data['email']) as $member) {
      if ($member['badge_no'] === ($params['badge_no'] ?? null)) {
        return $this->identity_for_member($member);
      }
    }
    throw new AuthFailure('unknown-member');
  }

  private function identity_for_member(array $member): Identity {
    return new Identity(
      $member['badge_no'],
      $member['email'],
      $member['name'],
      offer_password: !db_email_has_password_auth($member['email']) && !db_password_prompt_dismissed($member['email']),
    );
  }

  private function hash_code(string $code): string {
    require_config('crypto', 'signing_key');
    return hash_hmac('sha256', $code, config_get_secret('crypto', 'signing_key'));
  }

  private function normalize_email(string $email): ?string {
    $email = trim($email);
    if (strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
      return null;
    }
    return $email;
  }

  // Redirecting back to a login or password page would only confuse
  private function normalize_redirect(string $redirect): string {
    if (!is_valid_redirect($redirect)
      || str_starts_with($redirect, '/forgot-password')
      || str_starts_with($redirect, '/set-password')
      || str_starts_with($redirect, '/login')) {
      return '/';
    }
    return $redirect;
  }

  // $step is the template in templates/auth/local. Forms and links on it
  // return to $this->redirect.
  private function show_page(string $step, array $context, ?string $error = null): never {
    render_page("auth/local/$step.html.twig", [
      'error' => $error === null ? null : self::ERRORS[$error],
      'allow_anonymous' => ALLOW_ANONYMOUS_ACCESS,
      'redirect' => $this->redirect,
    ] + $context);
    exit;
  }

  private function render_entry(?string $error = null): never {
    $this->show_page('entry', [], $error);
  }

  private function render_options(string $email, ?string $error = null): never {
    $this->show_page('options', ['email' => $email], $error);
  }

  private function render_code_form(string $email, ?string $error = null): never {
    $this->show_page('enter-code', ['email' => $email], $error);
  }
}
