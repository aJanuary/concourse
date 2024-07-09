<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddMembershipBasedPermissions extends AbstractMigration
{
    public function up(): void
    {
        $this->table('permissions')
            ->insert([
                ['name' => 'online'],
                ['name' => 'in-person']
            ])->saveData();

        $this->execute("INSERT INTO role_permissions(role_id, permission_id) SELECT role_id, permission_id FROM roles JOIN permissions WHERE roles.name = 'online' AND permissions.name = 'online'");
        $this->execute("INSERT INTO role_permissions(role_id, permission_id) SELECT role_id, permission_id FROM roles JOIN permissions WHERE roles.name = 'in-person' AND permissions.name = 'in-person'");
        $this->execute("INSERT INTO role_permissions(role_id, permission_id) SELECT role_id, permission_id FROM roles JOIN permissions WHERE roles.name = 'wsfs' AND permissions.name = 'see-hugos'");
        $this->execute("INSERT INTO role_permissions(role_id, permission_id) SELECT role_id, permission_id FROM roles JOIN permissions WHERE roles.name = 'wsfs' AND permissions.name = 'see-site-selection'");
    }
}
