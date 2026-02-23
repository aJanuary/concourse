<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ReplaceLoginLinksWithLoginCodes extends AbstractMigration
{
    public function up(): void
    {
        $this->table('login_links')->drop()->save();

        $table = $this->table('login_codes');
        $table->addColumn('code', 'string', ['limit' => 6])
              ->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('redirect', 'string', ['limit' => 2000, 'default' => '/'])
              ->addColumn('expires_at', 'timestamp')
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['email'])
              ->addIndex(['code'])
              ->create();
    }

    public function down(): void
    {
        $this->table('login_codes')->drop()->save();

        $table = $this->table('login_links');
        $table->addColumn('badge_no', 'string', ['limit' => 20])
              ->addColumn('login_code', 'string', ['limit' => 255])
              ->addColumn('expires_at', 'integer')
              ->create();
    }
}
