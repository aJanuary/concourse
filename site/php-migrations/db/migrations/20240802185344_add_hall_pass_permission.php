<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddHallPassPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'hall-pass']
            ])->saveData();
    }
}
