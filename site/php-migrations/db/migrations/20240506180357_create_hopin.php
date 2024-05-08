<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateHopin extends AbstractMigration
{
    public function up(): void
    {
      $this->execute('CREATE TABLE rce_invites (
        badge_no VARCHAR(5) PRIMARY KEY,
        invite_url VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (badge_no) REFERENCES members(badge_no)
      );');
    }

    public function down(): void
    {
      $this->table('rce_invites')->drop()->save();
    }
}
