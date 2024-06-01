<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgZoomTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('prog_zoom', ['id' => false, 'primary_key' => ['zoom_url']]);
        $table->addColumn('zoom_url', 'string', ['limit' => 500, 'null' => false])
              ->create();
        $table->insert([['zoom_url' => '']])->saveData();
    }
}
