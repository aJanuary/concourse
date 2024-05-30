<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgStagesTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('prog_stages', ['id' => false, 'primary_key' => ['room_id']]);
        $table->addColumn('room_id', 'string', ['limit' => 36, 'null' => false])
              ->addColumn('type', 'enum', ['values' => ['hybrid', 'online-only']])
              ->addColumn('viewer_url', 'string', ['limit' => 100])
              ->addColumn('participant_url', 'string', ['limit' => 100])
              ->create();
    }
}
