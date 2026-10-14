<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPasswordAuth extends AbstractMigration
{
    public function up(): void
    {
      // last_edited is set by the application: ON UPDATE CURRENT_TIMESTAMP does not exist in Postgres
      $this->table('password_auth')
           ->addColumn('email', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('password_hash', 'string', ['limit' => 255, 'null' => false])
           ->addColumn('last_edited', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addIndex(['email'], ['unique' => true, 'name' => 'password_auth_email'])
           ->create();
    }

    public function down(): void
    {
      $this->table('password_auth')->drop()->save();
    }
}
