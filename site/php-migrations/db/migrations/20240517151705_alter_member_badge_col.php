<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterMemberBadgeCol extends AbstractMigration
{
    public function change(): void
    {
      $table = $this->table('members');
      // Clyde badge numbers start with # so we need the col a bit larger
      $table->changeColumn('badge_no', 'string', ['limit' => 6, 'null' => false])
            ->save();
    }
}
