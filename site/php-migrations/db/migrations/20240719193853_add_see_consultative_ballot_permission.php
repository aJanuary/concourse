<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeConsultativeBallotPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-consultive-ballot']
            ])->saveData();
    }
}