<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterMoreMemberBadgeCol extends AbstractMigration
{
    // Alter the badge_no columns in the rest of the tables to be consistent
    // Note: All tables including member_roles now handled by previous migrations
    // This migration is now a no-op but kept for historical compatibility
    public function up(): void
    {
      // All tables already updated by previous migrations
      // Nothing to do here
    }

    public function down(): void
    {
      // Nothing to reverse
    }
}
