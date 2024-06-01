<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveUniqueEmailConstraint extends AbstractMigration
{
    public function change(): void
    {
        $this->table('members')
             ->removeIndex(['email'])
             ->save();
    }
}
