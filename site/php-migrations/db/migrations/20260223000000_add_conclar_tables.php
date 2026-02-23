<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddConclarTables extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('CREATE TABLE conclar_selections (
            badge_no VARCHAR(10) NOT NULL,
            app_id VARCHAR(255) NOT NULL,
            item_id VARCHAR(255) NOT NULL,
            selected BOOLEAN NOT NULL,
            PRIMARY KEY (badge_no, app_id, item_id),
            FOREIGN KEY (badge_no) REFERENCES members(badge_no)
        )');
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS conclar_selections');
    }
}
