<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ReplaceLoginLinksWithLoginCodes extends AbstractMigration
{
    public function up(): void
    {
      // Outstanding links were short lived, and nothing creates them any more
      if ($this->hasTable('login_links')) {
        $this->table('login_links')->drop()->save();
      }

      $this->table('login_codes')
           ->addColumn('email', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('code_hash', 'string', ['limit' => 64, 'null' => false])
           ->addColumn('failed_attempts', 'integer', ['default' => 0, 'null' => false])
           ->addColumn('expires_at', 'timestamp', ['null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addIndex(['email'])
           ->create();
    }

    public function down(): void
    {
      $this->table('login_codes')->drop()->save();

      $this->table('login_links', ['id' => false, 'primary_key' => ['login_code']])
           ->addColumn('login_code', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
           ->addColumn('expires_at', 'timestamp', ['null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addForeignKey('badge_no', 'members', 'badge_no')
           ->create();
    }
}
