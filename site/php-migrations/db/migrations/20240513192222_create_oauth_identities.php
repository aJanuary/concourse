<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOauthIdentities extends AbstractMigration
{
    public function change(): void
    {
      $table = $this->table('oauth_identities', ['id' => false, 'primary_key' => ['provider', 'identity_id']]);
      $table->addColumn('badge_no', 'string', ['limit' => 6])
            ->addColumn('provider', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('identity_id', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('raw_info', 'json')
            ->addColumn('create_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();

      $table->addIndex(['identity_id'], ['unique' => true])
            ->addIndex(['badge_no', 'provider'], ['unique' => true])
            ->addForeignKey('badge_no', 'members', 'badge_no', ['delete'=> 'CASCADE'])
            ->save();
    }
}
