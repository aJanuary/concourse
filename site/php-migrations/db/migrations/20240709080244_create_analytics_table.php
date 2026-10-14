<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAnalyticsTable extends AbstractMigration
{
    public function up(): void
    {
        // Phinx has no enum type for Postgres
        $is_pgsql = $this->getAdapter()->getAdapterType() === 'pgsql';
        $table = $this->table('analytics');
        $table->addColumn('date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('member_type', $is_pgsql ? 'string' : 'enum', $is_pgsql ? ['limit' => 20] : ['values' => ['in-person', 'online', 'anonymous', 'not-logged-in']])
              ->addColumn('event', 'string', ['limit' => 100])
              ->addColumn('data', 'string', ['limit' => 2048])
              ->create();

        if ($is_pgsql) {
            $this->execute("ALTER TABLE analytics ADD CONSTRAINT analytics_member_type_check CHECK (member_type IN ('in-person', 'online', 'anonymous', 'not-logged-in'))");
        }
    }
}
