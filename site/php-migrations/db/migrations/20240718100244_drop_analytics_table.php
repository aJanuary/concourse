<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropAnalyticsTable extends AbstractMigration
{
    public function up(): void
    {
        $this->table('analytics')->drop()->save();
    }
}
