<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateZoomEventsTables extends AbstractMigration
{
    public function change(): void
    {
        $this->table('zoom_events_tickets', ['id' => false, 'primary_key' => ['badge_no']])
            ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('ticket_id', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('event_join_link', 'string', ['limit' => 1000, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
            ->addForeignKey('badge_no', 'members', 'badge_no', ['delete' => 'CASCADE'])
            ->create();

        $this->table('auth_token_cache', ['id' => false, 'primary_key' => ['service']])
            ->addColumn('service', 'string', ['limit' => 30, 'null' => false])
            ->addColumn('auth_token', 'string', ['limit' => 1000, 'null' => false])
            ->addColumn('expires_at', 'integer', ['null' => false])
            ->create();
    }
}
