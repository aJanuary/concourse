<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddMembershipbasedRoles extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('roles');
        $table->insert([
            ['name' => 'online'],
            ['name' => 'in-person'],
            ['name' => 'wsfs'],
        ])->saveData();
    }
}
