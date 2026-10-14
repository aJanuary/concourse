<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

// A member with <card permission>-coming-soon but not the card permission
// sees the card greyed out in the home page's "Coming soon…" section
final class AddComingSoonPermissions extends AbstractMigration
{
    private const PERMISSIONS = [
        'has-childcare-coming-soon',
        'see-accessibility-coming-soon',
        'see-benefits-coming-soon',
        'see-coc-coming-soon',
        'see-discord-coming-soon',
        'see-gallery-coming-soon',
        'see-guide-coming-soon',
        'see-help-coming-soon',
        'see-itinerary-coming-soon',
        'see-maps-coming-soon',
        'see-participant-guides-coming-soon',
        'see-rce-coming-soon',
        'see-readme-coming-soon',
        'see-signups-coming-soon',
        'see-time-coming-soon',
        'see-times-coming-soon',
        'see-tours-coming-soon',
        'see-upload-photo-coming-soon',
        'see-upload-presentation-coming-soon',
        'see-virtual-gathering-coming-soon',
        'see-website-coming-soon',
    ];

    public function up(): void
    {
        $this->table('permissions')
            ->insert(array_map(fn($name) => ['name' => $name], self::PERMISSIONS))
            ->saveData();
    }

    public function down(): void
    {
        $names = implode(', ', array_map(fn($name) => "'" . $name . "'", self::PERMISSIONS));
        $this->execute("DELETE FROM permissions WHERE name IN ($names)");
    }
}
