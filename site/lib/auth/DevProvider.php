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
    require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

    render_header('Log in as a member', 'Development login.', ['Home' => '/', 'Log in']);
    ?>
    <article>
      <h2>Log in as a member</h2>
      <form method="post" action="/login?provider=dev">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        <label for="badge_no">Member</label>
        <select name="badge_no" id="badge_no">
          <?php foreach (db_get_members() as $member) { ?>
            <option value="<?php echo htmlspecialchars($member['badge_no']); ?>"><?php echo htmlspecialchars($member['badge_no'] . ' ' . $member['name']); ?></option>
          <?php } ?>
        </select>
        <button type="submit" class="button login">Log in</button>
      </form>
    </article>
    <?php
    render_footer();
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
