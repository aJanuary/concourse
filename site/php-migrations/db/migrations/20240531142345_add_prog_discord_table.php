<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgDiscordTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('prog_discord_posts', ['id' => false, 'primary_key' => ['item_id']]);
        $table->addColumn('item_id', 'string', ['limit' => 36, 'null' => false])
              ->addColumn('start', 'integer')
              ->addColumn('duration', 'integer')
              ->addColumn('room_id', 'string', ['limit' => 36])
              ->addColumn('post_url', 'string', ['limit' => 500])
              ->create();

    }
}
