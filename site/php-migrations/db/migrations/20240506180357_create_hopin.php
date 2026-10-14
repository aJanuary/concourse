<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateHopin extends AbstractMigration
{
    public function up(): void
    {
      $this->table('rce_invites', ['id' => false, 'primary_key' => ['badge_no']])
           ->addColumn('badge_no', 'string', ['limit' => 5, 'null' => false])
           ->addColumn('invite_url', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addForeignKey('badge_no', 'members', 'badge_no')
           ->create();
    }

    public function down(): void
    {
      $this->table('rce_invites')->drop()->save();
    }
}
