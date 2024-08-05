<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeSignupLinks extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-signup-links']
            ])->saveData();
    }
}
