<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgReplyTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('prog_replay', ['id' => false, 'primary_key' => ['item_id']]);
        $table->addColumn('item_id', 'string', ['limit' => 36, 'null' => false])
              ->addColumn('replay_url', 'string', ['limit' => 500])
              ->create();

    }
}
