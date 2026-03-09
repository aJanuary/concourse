<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddFanFundsAndDocWeirPermissions extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-fan-funds'],
                ['name' => 'see-doc-weir'],
            ])->saveData();
    }
}
