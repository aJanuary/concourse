<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddManageProgrammePermission extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('permissions');
        $table->insert([
            ['name' => 'manage-programme'],
        ])->saveData();
    }
}
