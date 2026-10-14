<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddMemberRealName extends AbstractMigration
{
    public function up(): void
    {
      $this->table('members')
           ->addColumn('real_name', 'string', ['limit' => 100, 'null' => true])
           ->update();
    }

    public function down(): void
    {
      $this->table('members')->removeColumn('real_name')->update();
    }
}
