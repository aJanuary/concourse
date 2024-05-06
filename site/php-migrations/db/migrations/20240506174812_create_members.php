<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMembers extends AbstractMigration
{
    public function up(): void
    {
      $this->execute('CREATE TABLE members (
          badge_no VARCHAR(5) PRIMARY KEY,
          email VARCHAR(100) NOT NULL,
          name VARCHAR(100) NOT NULL,
          created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );');
      $this->execute('CREATE UNIQUE INDEX members_email ON members(email);');

      $this->execute('CREATE TABLE sessions (
        session_id VARCHAR(100) PRIMARY KEY,
        badge_no VARCHAR(5) NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (badge_no) REFERENCES members(badge_no)
      );');

      $this->execute('CREATE TABLE login_links (
        login_code VARCHAR(100) PRIMARY KEY,
        badge_no VARCHAR(5) NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (badge_no) REFERENCES members(badge_no)
      );');
    }

    public function down(): void
    {
      $this->table('login_links')->drop()->save();
      $this->table('sessions')->drop()->save();
      $this->table('members')->drop()->save();
    }
}



