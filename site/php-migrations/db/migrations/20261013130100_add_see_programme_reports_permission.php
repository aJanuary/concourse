<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeProgrammeReportsPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-programme-reports']
            ])->saveData();
    }

    public function down(): void
    {
        $this->execute("DELETE FROM permissions WHERE name = 'see-programme-reports'");
    }
}
