<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgStreamUrlsTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('prog_stream_urls', ['id' => false, 'primary_key' => ['room_id']]);
        $table->addColumn('room_id', 'string', ['limit' => 36, 'null' => false])
              ->addColumn('title', 'string', ['limit' => 256, 'null' => true, 'default' => null])
              ->addColumn('url', 'string', ['limit' => 2048, 'null' => true, 'default' => null])
              ->create();
    }
}
