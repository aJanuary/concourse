<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSeeUploadPhotoPermission extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'see-upload-photo']
            ])->saveData();
    }
}
