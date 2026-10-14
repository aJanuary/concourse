<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRoles extends AbstractMigration
{
    public function up(): void
    {
      $this->table('roles', ['id' => false, 'primary_key' => ['role_id']])
           ->addColumn('role_id', 'biginteger', ['identity' => true, 'signed' => false])
           ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
           ->create();

      $this->table('member_roles', ['id' => false, 'primary_key' => ['badge_no', 'role_id']])
           ->addColumn('badge_no', 'string', ['limit' => 5, 'null' => false])
           ->addColumn('role_id', 'biginteger', ['signed' => false, 'null' => false])
           ->addForeignKey('badge_no', 'members', 'badge_no')
           ->addForeignKey('role_id', 'roles', 'role_id')
           ->create();

      $this->table('role_permissions', ['id' => false, 'primary_key' => ['role_id', 'permission']])
           ->addColumn('role_id', 'biginteger', ['signed' => false, 'null' => false])
           ->addColumn('permission', 'string', ['limit' => 255, 'null' => false])
           ->addForeignKey('role_id', 'roles', 'role_id')
           ->create();

      $this->execute("INSERT INTO roles(name) VALUES ('default'), ('admin'), ('moderator'), ('programme participant'), ('programme moderator'), ('artist'), ('fan table'), ('dealer');");
      $this->execute("INSERT INTO roles(name) VALUES ('admin');");
      $this->execute("INSERT INTO roles(name) VALUES ('moderator');");
      $this->execute("INSERT INTO role_permissions(role_id, permission) SELECT role_id, 'manage-discord-ids' FROM roles WHERE name = 'moderator';");
      $this->execute("INSERT INTO role_permissions(role_id, permission) SELECT role_id, 'see-participant-guides' FROM roles WHERE name IN ('programme participant', 'programme moderator');");
    }

    public function down(): void
    {
      $this->table('role_permissions')->drop()->save();
      $this->table('member_roles')->drop()->save();
      $this->table('roles')->drop()->save();
    }
}
