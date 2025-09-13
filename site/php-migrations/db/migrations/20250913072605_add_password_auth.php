<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPasswordAuth extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('password_auth');
        $table->addColumn('email', 'string', ['limit' => 100])
              ->addColumn('password_hash', 'string', ['limit' => 255])
              ->addColumn('last_edited', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->create();
        $table->addIndex(['email'], ['unique' => true])->save();

    }
}
