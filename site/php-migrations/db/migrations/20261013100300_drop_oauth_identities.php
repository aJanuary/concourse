<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DropOauthIdentities extends AbstractMigration
{
    public function up(): void
    {
      $this->table('oauth_identities')->drop()->save();
    }

    // Recreates the table empty: the rows are not recoverable
    public function down(): void
    {
      $this->table('oauth_identities', ['id' => false, 'primary_key' => ['provider', 'identity_id']])
           ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
           ->addColumn('provider', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('identity_id', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('email', 'string', ['limit' => 100])
           ->addColumn('raw_info', 'json')
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
           ->addIndex(['identity_id'], ['unique' => true])
           ->addIndex(['badge_no', 'provider'], ['unique' => true])
           ->addForeignKey('badge_no', 'members', 'badge_no', ['delete' => 'CASCADE'])
           ->create();
    }
}
