<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddInMemoriamPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-in-memoriam']
            ])->saveData();
    }
}
