<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTitleColumnToProgTables extends AbstractMigration
{
    public function up(): void
    {
        foreach ([
            'prog_discord_posts',
            'prog_replay',
            'prog_sessions',
            'prog_stages'
        ] as $table) {
            $this->table($table)
                 ->addColumn('title', 'string', ['limit' => 256, 'null' => true])
                 ->save();
        }
    }
}
