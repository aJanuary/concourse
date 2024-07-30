<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeReadmeRedirect extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-readme-redirect']
            ])->saveData();
    }
}
