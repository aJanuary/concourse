<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAnalyticsTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('analytics');
        $table->addColumn('date', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('member_type', 'enum', ['values' => ['in-person', 'online', 'anonymous', 'not-logged-in']])
              ->addColumn('event', 'string', ['limit' => 100])
              ->addColumn('data', 'string', ['limit' => 2048])
              ->create();
    }
}
