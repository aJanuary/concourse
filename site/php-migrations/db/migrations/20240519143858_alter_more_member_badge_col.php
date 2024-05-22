<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterMoreMemberBadgeCol extends AbstractMigration
{
    // Alter the badge_no columns in the rest of the tables to be consistent
    public function change(): void
    {
      $table = $this->table('discord_ids');
      $table->changeColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->save();

      $table = $this->table('login_links');
      $table->changeColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->save();

      $table = $this->table('member_roles');
      $table->changeColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->save();

      $table = $this->table('rce_invites');
      $table->changeColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->save();

      $table = $this->table('sessions');
      $table->changeColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->save();
    }
}
