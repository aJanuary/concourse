<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgSessionsTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('prog_sessions', ['id' => false, 'primary_key' => ['item_id']]);
        $table->addColumn('item_id', 'string', ['limit' => 36, 'null' => false])
              ->addColumn('rce_url', 'string', ['limit' => 500])
              ->create();

    }
}
