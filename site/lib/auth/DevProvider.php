<?php

require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');
require_once(__DIR__ . '/AuthProvider.php');

// Logs in as any existing member, without credentials. Development only.
class DevProvider implements AuthProvider {
  public static function is_available(): bool {
    return getenv('RUNTIME_ENV') === 'development';
  }

  private function require_development(): void {
    if (!self::is_available()) {
      http_response_code(403);
      throw new RuntimeException('The dev login provider is only available when RUNTIME_ENV is development');
    }
  }

  public function id(): string {
    return 'dev';
  }

  public function display_name(): string {
    return 'Log in as an existing member (development only)';
  }

  public function begin(string $redirect): void {
    $this->require_development();
    require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

    render_page('auth/dev/begin.html.twig', [
      'title' => 'Log in as a member',
      'description' => 'Development login.',
      'breadcrumbs' => ['Home' => '/', 'Log in'],
      'redirect' => $redirect,
      'members' => db_get_members(),
    ]);
    exit;
  }

  public function complete(array $params): Identity {
    $this->require_development();

    $badge_no = $params['badge_no'] ?? '';
    if (!db_member_exists($badge_no)) {
      throw new AuthFailure('unknown-member');
    }
    $member = db_get_member($badge_no);

    return new Identity($badge_no, $member['email'], $member['name']);
  }
}
