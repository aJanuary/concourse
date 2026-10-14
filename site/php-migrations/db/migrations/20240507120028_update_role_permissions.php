<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateRolePermissions extends AbstractMigration
{
    public function up(): void
    {
      //  Clean up the duplicate admin and moderator roles
      $duplicates = $this->fetchAll("
        SELECT dup.role_id
        FROM roles dup
        JOIN roles keep ON keep.name = dup.name AND keep.role_id < dup.role_id
        WHERE dup.name IN ('admin', 'moderator')
      ");
      foreach ($duplicates as $duplicate) {
        $this->execute('DELETE FROM role_permissions WHERE role_id = ' . (int) $duplicate['role_id']);
        $this->execute('DELETE FROM roles WHERE role_id = ' . (int) $duplicate['role_id']);
      }

      // Create permission table structure
      $this->table('permissions', ['id' => false, 'primary_key' => ['permission_id']])
           ->addColumn('permission_id', 'biginteger', ['identity' => true, 'signed' => false])
           ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
           ->addIndex(['name'], ['unique' => true, 'name' => 'permissions_name_uk'])
           ->create();
      $this->execute("
        INSERT INTO permissions(name) VALUES
          ('see-readme'),
          ('see-guide'),
          ('see-discord'),
          ('see-newsletter'),
          ('see-souvenir'),
          ('see-vote'),
          ('see-rce'),
          ('see-participant-guides'),
          ('manage-roles'),
          ('manage-discord-ids');
      ");

      // Rebuild role_permissions to reference permissions by id rather than name
      $old_rows = $this->fetchAll('SELECT role_id, permission FROM role_permissions');
      $this->table('role_permissions')->drop()->save();

      $this->table('role_permissions', ['id' => false, 'primary_key' => ['role_id', 'permission_id']])
           ->addColumn('role_id', 'biginteger', ['signed' => false, 'null' => false])
           ->addColumn('permission_id', 'biginteger', ['signed' => false, 'null' => false])
           ->addForeignKey('role_id', 'roles', 'role_id', ['delete' => 'CASCADE'])
           ->addForeignKey('permission_id', 'permissions', 'permission_id', ['delete' => 'CASCADE'])
           ->create();

      foreach ($old_rows as $row) {
        $this->execute(
          'INSERT INTO role_permissions(role_id, permission_id) SELECT ' . (int) $row['role_id'] . ', permission_id FROM permissions WHERE name = '
          . $this->getAdapter()->getConnection()->quote($row['permission'])
        );
      }

      // Add missing constraints
      $this->table('roles')->addIndex(['name'], ['unique' => true])->update();
      $this->table('member_roles')->addIndex(['badge_no', 'role_id'], ['unique' => true])->update();
    }
}
