<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPermissions extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('permissions');
        $table->insert([
            ['name' => 'see-time'],
            ['name' => 'see-maps'],
            ['name' => 'see-help'],
            ['name' => 'see-signups'],
            ['name' => 'see-gallery'],
            ['name' => 'see-itinerary'],
            ['name' => 'see-hugos'],
            ['name' => 'see-site-selection'],
            ['name' => 'see-childcare'],
            ['name' => 'has-childcare'],
            ['name' => 'see-website'],
            ['name' => 'see-itinerary-other'],
            ['name' => 'see-participant-guides-other'],
        ])->saveData();
    }
}
