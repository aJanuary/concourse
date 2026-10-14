<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateConclarSelections extends AbstractMigration
{
    public function up(): void
    {
      $this->table('conclar_selections', ['id' => false, 'primary_key' => ['badge_no', 'app_id', 'item_id']])
           ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
           ->addColumn('app_id', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('item_id', 'string', ['limit' => 255, 'null' => false])
           ->addColumn('selected', 'boolean', ['null' => false])
           ->addForeignKey('badge_no', 'members', 'badge_no', ['delete' => 'CASCADE'])
           ->create();
    }

    public function down(): void
    {
      $this->table('conclar_selections')->drop()->save();
    }
}
