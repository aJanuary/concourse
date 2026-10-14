<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddOidcSessions extends AbstractMigration
{
    public function up(): void
    {
      // Links a portal session to the provider's session (sid) and user (sub),
      // so a back-channel logout can end the right sessions. Not every
      // provider sends a sid, hence nullable.
      $this->table('oidc_sessions', ['id' => false, 'primary_key' => ['session_id']])
           ->addColumn('session_id', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('sid', 'string', ['limit' => 200, 'null' => true])
           ->addColumn('sub', 'string', ['limit' => 200, 'null' => false])
           // Sent back as the hint when logging out at the provider
           ->addColumn('id_token', 'text', ['null' => true])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addIndex(['sid'])
           ->addIndex(['sub'])
           ->addForeignKey('session_id', 'sessions', 'session_id', ['delete' => 'CASCADE'])
           ->create();
    }

    public function down(): void
    {
      $this->table('oidc_sessions')->drop()->save();
    }
}
