<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeGuideRedirect extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-guide-redirect']
            ])->saveData();
    }
}
