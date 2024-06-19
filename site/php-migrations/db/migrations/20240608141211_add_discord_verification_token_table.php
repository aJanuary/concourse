<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddDiscordVerificationTokenTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('discord_verification_token', ['id' => false, 'primary_key' => ['token_id']]);
        $table->addColumn('token_id', 'string', ['limit' => 41, 'null' => false])
              ->addColumn('discord_id', 'string', ['limit' => 100])
              ->addColumn('username', 'string', ['limit' => 100])
              ->addColumn('verified_at', 'timestamp', ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->create();

    }
}
