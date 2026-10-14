<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

// RingCentral Events invite links are no longer used
final class DropRceInvites extends AbstractMigration
{
    public function up(): void
    {
      $this->table('rce_invites')->drop()->save();
      $this->execute("DELETE FROM permissions WHERE name = 'see-rce-link'");
    }

    // Recreates the table empty and the permission ungranted: the rows are
    // not recoverable
    public function down(): void
    {
      $this->table('rce_invites', ['id' => false, 'primary_key' => ['badge_no']])
           ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
           ->addColumn('invite_url', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addForeignKey('badge_no', 'members', 'badge_no')
           ->create();
      $this->table('permissions')
           ->insert([
               ['name' => 'see-rce-link']
           ])->saveData();
    }
}
