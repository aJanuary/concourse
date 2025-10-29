<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterMemberBadgeCol extends AbstractMigration
{
    public function up(): void
    {
      // Clyde badge numbers start with # so we need the col a bit larger
      // We need to drop foreign keys, alter columns, then recreate FKs

      // Drop foreign keys (check if they exist first)
      $this->execute('ALTER TABLE sessions DROP FOREIGN KEY IF EXISTS sessions_ibfk_1');
      $this->execute('ALTER TABLE sessions DROP FOREIGN KEY IF EXISTS fk_sessions_badge');
      $this->execute('ALTER TABLE login_links DROP FOREIGN KEY IF EXISTS login_links_ibfk_1');
      $this->execute('ALTER TABLE login_links DROP FOREIGN KEY IF EXISTS fk_login_links_badge');
      $this->execute('ALTER TABLE rce_invites DROP FOREIGN KEY IF EXISTS rce_invites_ibfk_1');
      $this->execute('ALTER TABLE rce_invites DROP FOREIGN KEY IF EXISTS fk_rce_invites_badge');
      $this->execute('ALTER TABLE discord_ids DROP FOREIGN KEY IF EXISTS discord_ids_ibfk_1');
      $this->execute('ALTER TABLE discord_ids DROP FOREIGN KEY IF EXISTS fk_discord_ids_badge');
      $this->execute('ALTER TABLE oauth_identities DROP FOREIGN KEY IF EXISTS oauth_identities_ibfk_1');
      $this->execute('ALTER TABLE oauth_identities DROP FOREIGN KEY IF EXISTS fk_oauth_identities_badge');
      $this->execute('ALTER TABLE member_roles DROP FOREIGN KEY IF EXISTS member_roles_ibfk_1');
      $this->execute('ALTER TABLE member_roles DROP FOREIGN KEY IF EXISTS fk_member_roles_badge');

      // Alter badge_no columns in all tables
      $this->execute('ALTER TABLE members MODIFY badge_no VARCHAR(6) NOT NULL');
      $this->execute('ALTER TABLE sessions MODIFY badge_no VARCHAR(6) NOT NULL');
      $this->execute('ALTER TABLE login_links MODIFY badge_no VARCHAR(6) NOT NULL');
      $this->execute('ALTER TABLE rce_invites MODIFY badge_no VARCHAR(6) NOT NULL');
      $this->execute('ALTER TABLE discord_ids MODIFY badge_no VARCHAR(6) NOT NULL');
      $this->execute('ALTER TABLE oauth_identities MODIFY badge_no VARCHAR(6) NOT NULL');
      $this->execute('ALTER TABLE member_roles MODIFY badge_no VARCHAR(6) NOT NULL');

      // Recreate foreign keys with explicit names
      $this->execute('ALTER TABLE sessions ADD CONSTRAINT fk_sessions_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE login_links ADD CONSTRAINT fk_login_links_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE rce_invites ADD CONSTRAINT fk_rce_invites_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE discord_ids ADD CONSTRAINT fk_discord_ids_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE oauth_identities ADD CONSTRAINT fk_oauth_identities_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no) ON DELETE CASCADE');
      $this->execute('ALTER TABLE member_roles ADD CONSTRAINT fk_member_roles_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no) ON DELETE CASCADE');
    }

    public function down(): void
    {
      // Reverse the changes
      $this->execute('ALTER TABLE sessions DROP FOREIGN KEY IF EXISTS fk_sessions_badge');
      $this->execute('ALTER TABLE login_links DROP FOREIGN KEY IF EXISTS fk_login_links_badge');
      $this->execute('ALTER TABLE rce_invites DROP FOREIGN KEY IF EXISTS fk_rce_invites_badge');
      $this->execute('ALTER TABLE discord_ids DROP FOREIGN KEY IF EXISTS fk_discord_ids_badge');
      $this->execute('ALTER TABLE oauth_identities DROP FOREIGN KEY IF EXISTS fk_oauth_identities_badge');
      $this->execute('ALTER TABLE member_roles DROP FOREIGN KEY IF EXISTS fk_member_roles_badge');

      $this->execute('ALTER TABLE members MODIFY badge_no VARCHAR(5) NOT NULL');
      $this->execute('ALTER TABLE sessions MODIFY badge_no VARCHAR(5) NOT NULL');
      $this->execute('ALTER TABLE login_links MODIFY badge_no VARCHAR(5) NOT NULL');
      $this->execute('ALTER TABLE rce_invites MODIFY badge_no VARCHAR(5) NOT NULL');
      $this->execute('ALTER TABLE discord_ids MODIFY badge_no VARCHAR(5) NOT NULL');
      $this->execute('ALTER TABLE oauth_identities MODIFY badge_no VARCHAR(5) NOT NULL');
      $this->execute('ALTER TABLE member_roles MODIFY badge_no VARCHAR(5) NOT NULL');

      $this->execute('ALTER TABLE sessions ADD CONSTRAINT fk_sessions_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE login_links ADD CONSTRAINT fk_login_links_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE rce_invites ADD CONSTRAINT fk_rce_invites_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE discord_ids ADD CONSTRAINT fk_discord_ids_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no)');
      $this->execute('ALTER TABLE oauth_identities ADD CONSTRAINT fk_oauth_identities_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no) ON DELETE CASCADE');
      $this->execute('ALTER TABLE member_roles ADD CONSTRAINT fk_member_roles_badge FOREIGN KEY (badge_no) REFERENCES members(badge_no) ON DELETE CASCADE');
    }
}
