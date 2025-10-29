<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOauthIdentities extends AbstractMigration
{
    public function change(): void
    {
      // Use raw SQL to ensure consistent charset/collation with members table
      $this->execute('CREATE TABLE oauth_identities (
        badge_no VARCHAR(5) NOT NULL,
        provider VARCHAR(100) NOT NULL,
        identity_id VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        raw_info JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (provider, identity_id),
        UNIQUE KEY unique_identity_id (identity_id),
        UNIQUE KEY unique_badge_provider (badge_no, provider),
        FOREIGN KEY (badge_no) REFERENCES members(badge_no) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }
}
