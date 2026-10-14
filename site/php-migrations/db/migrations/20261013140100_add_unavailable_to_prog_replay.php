<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUnavailableToProgReplay extends AbstractMigration
{
    public function change(): void
    {
        $this->table('prog_replay')
            ->addColumn('unavailable', 'boolean', ['null' => false, 'default' => false])
            ->update();
    }
}
