<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgStagesTable extends AbstractMigration
{
    public function up(): void
    {
        // Phinx has no enum type for Postgres
        $is_pgsql = $this->getAdapter()->getAdapterType() === 'pgsql';
        $table = $this->table('prog_stages', ['id' => false, 'primary_key' => ['room_id']]);
        $table->addColumn('room_id', 'string', ['limit' => 36, 'null' => false])
              ->addColumn('type', $is_pgsql ? 'string' : 'enum', $is_pgsql ? ['limit' => 20] : ['values' => ['hybrid', 'online-only']])
              ->addColumn('viewer_url', 'string', ['limit' => 100])
              ->addColumn('participant_url', 'string', ['limit' => 100])
              ->create();

        if ($is_pgsql) {
            $this->execute("ALTER TABLE prog_stages ADD CONSTRAINT prog_stages_type_check CHECK (type IN ('hybrid', 'online-only'))");
        }
    }
}
