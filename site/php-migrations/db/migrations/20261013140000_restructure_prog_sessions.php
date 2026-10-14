<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RestructureProgSessions extends AbstractMigration
{
    public function up(): void
    {
        $this->table('prog_sessions')
            ->renameColumn('rce_url', 'destination')
            ->addColumn('handler', 'string', ['limit' => 20, 'null' => true])
            ->update();
    }

    public function down(): void
    {
        $this->table('prog_sessions')
            ->removeColumn('handler')
            ->renameColumn('destination', 'rce_url')
            ->update();
    }
}
