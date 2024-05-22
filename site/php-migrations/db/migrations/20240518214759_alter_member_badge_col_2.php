<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterMemberBadgeCol2 extends AbstractMigration
{
    public function change(): void
    {
      // Clyde badge numbers start with # so we need the col a bit larger
      // Making this 10 chars to be safe
      $table = $this->table('members');
      $table->changeColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->save();

      $table = $this->table('oauth_identities');
      $table->changeColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->save();
    }
}
