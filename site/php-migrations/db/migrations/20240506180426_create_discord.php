<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateDiscord extends AbstractMigration
{
    public function up(): void
    {
      $this->table('discord_ids', ['id' => false, 'primary_key' => ['badge_no', 'discord_id']])
           ->addColumn('badge_no', 'string', ['limit' => 5, 'null' => false])
           ->addColumn('discord_id', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('username', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addForeignKey('badge_no', 'members', 'badge_no')
           ->create();
    }

    public function down(): void
    {
      $this->table('discord_ids')->drop()->save();
    }
}
