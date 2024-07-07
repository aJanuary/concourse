<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeTimesPermission extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('permissions');
        $table->insert([
            ['name' => 'see-times'],
        ])->saveData();
    }
}
