<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeVolunteerPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-volunteer']
            ])->saveData();
    }
}
