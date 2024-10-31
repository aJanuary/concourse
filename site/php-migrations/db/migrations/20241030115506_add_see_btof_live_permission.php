<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeBtofLivePermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-btof-live']
            ])->saveData();
    }
}
