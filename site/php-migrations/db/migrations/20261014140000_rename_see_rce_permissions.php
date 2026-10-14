<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

// The stream card and joining programme items are no longer tied to
// RingCentral Events. Roles keep their grants, which refer to the id.
final class RenameSeeRcePermissions extends AbstractMigration
{
    private const RENAMES = [
        'see-rce' => 'see-stream',
        'see-rce-coming-soon' => 'see-stream-coming-soon',
    ];

    public function up(): void
    {
        foreach (self::RENAMES as $old => $new) {
            $this->execute("UPDATE permissions SET name = '$new' WHERE name = '$old'");
        }
    }

    public function down(): void
    {
        foreach (self::RENAMES as $old => $new) {
            $this->execute("UPDATE permissions SET name = '$old' WHERE name = '$new'");
        }
    }
}
