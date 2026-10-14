<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

// Each member's personal join link for the business meeting platform. There
// is no foreign key to members, because links are uploaded before members
// have logged in. Times are unix timestamps.
final class CreateBusinessMeeting extends AbstractMigration
{
    private const PERMISSIONS = [
        'see-business-meeting',
        'see-business-meeting-coming-soon',
        'manage-business-meeting',
    ];

    public function up(): void
    {
        $this->table('business_meeting_links', ['id' => false, 'primary_key' => ['badge_no']])
            ->addColumn('badge_no', 'string', ['limit' => 36, 'null' => false])
            ->addColumn('join_link', 'string', ['limit' => 2048, 'null' => false])
            ->addColumn('created_at', 'biginteger', ['null' => false])
            ->addColumn('updated_at', 'biginteger', ['null' => false])
            ->create();

        $this->table('roles')
            ->insert([['name' => 'business-meeting']])
            ->saveData();

        $this->table('permissions')
            ->insert(array_map(fn($name) => ['name' => $name], self::PERMISSIONS))
            ->saveData();

        $this->execute("INSERT INTO role_permissions (role_id, permission_id)
            SELECT role_id, permission_id
            FROM roles CROSS JOIN permissions
            WHERE roles.name = 'business-meeting' AND permissions.name = 'see-business-meeting'");
    }

    public function down(): void
    {
        $names = implode(', ', array_map(fn($name) => "'" . $name . "'", self::PERMISSIONS));
        $this->execute("DELETE FROM role_permissions WHERE permission_id IN (SELECT permission_id FROM permissions WHERE name IN ($names))");
        $this->execute("DELETE FROM role_permissions WHERE role_id IN (SELECT role_id FROM roles WHERE name = 'business-meeting')");
        $this->execute("DELETE FROM member_roles WHERE role_id IN (SELECT role_id FROM roles WHERE name = 'business-meeting')");
        $this->execute("DELETE FROM permissions WHERE name IN ($names)");
        $this->execute("DELETE FROM roles WHERE name = 'business-meeting'");
        $this->table('business_meeting_links')->drop()->save();
    }
}
