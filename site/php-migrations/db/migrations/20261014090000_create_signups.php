<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

// Times are unix timestamps so they don't depend on the database or PHP time zone
final class CreateSignups extends AbstractMigration
{
    public function change(): void
    {
        $this->table('signups')
            ->addColumn('title', 'string', ['limit' => 256, 'null' => false])
            ->addColumn('category', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('url', 'string', ['limit' => 2048, 'null' => false])
            ->addColumn('guide_url', 'string', ['limit' => 2048, 'null' => true, 'default' => null])
            ->addColumn('notes', 'text', ['null' => true, 'default' => null])
            ->addColumn('session_start', 'biginteger', ['null' => true, 'default' => null])
            ->addColumn('opens_at', 'biginteger', ['null' => true, 'default' => null])
            ->addColumn('closes_at', 'biginteger', ['null' => true, 'default' => null])
            ->create();
    }
}
