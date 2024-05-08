<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateDiscord extends AbstractMigration
{
    public function up(): void
    {
      $this->execute('CREATE TABLE discord_ids (
        badge_no VARCHAR(5) NOT NULL,
        discord_id VARCHAR(100) NOT NULL,
        username VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (badge_no, discord_id),
        FOREIGN KEY (badge_no) REFERENCES members(badge_no)
      );');
    }

    public function down(): void
    {
      $this->table('discord_ids')->drop()->save();
    }
}
