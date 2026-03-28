<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgrammeHandoutsPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-programme-handouts'],
            ])->saveData();
    }
}
