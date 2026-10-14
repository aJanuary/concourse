<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

// Site selection: the pool of voter keys ("tokens") from the election
// platform and who each is assigned to, the Square payment links made for
// the fee, and the address and preferences members give when paying. Times
// are unix timestamps.
//
// A site_selection_tokens row is either a token in the pool (method null
// until it is assigned, then online) or a paper ballot sold at the desk
// (method paper, no token). A member has at most one row that isn't spoilt;
// db.php enforces that, as a partial unique index isn't portable to MySQL.
final class CreateSiteSelection extends AbstractMigration
{
    private const PERMISSIONS = [
        'see-site-selection',
        'see-site-selection-coming-soon',
        'sell-tokens',
        'manage-tokens',
    ];

    public function up(): void
    {
        // Voter keys are case sensitive, which MySQL's default collation isn't
        $token_options = ['limit' => 100, 'null' => true];
        if ($this->getAdapter()->getAdapterType() === 'mysql') {
            $token_options['collation'] = 'utf8mb4_bin';
        }
        $this->table('site_selection_tokens')
            ->addColumn('token', 'string', $token_options)
            ->addColumn('label', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => true])
            ->addColumn('method', 'string', ['limit' => 10, 'null' => true])
            ->addColumn('sold_by', 'string', ['limit' => 10, 'null' => true])
            ->addColumn('sold_at', 'biginteger', ['null' => true])
            ->addColumn('spoilt_at', 'biginteger', ['null' => true])
            ->addColumn('added_by', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('added_at', 'biginteger', ['null' => false])
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['badge_no'])
            ->addForeignKey('badge_no', 'members', 'badge_no')
            ->create();

        $this->table('site_selection_payment_links', ['id' => false, 'primary_key' => ['payment_link_id']])
            ->addColumn('payment_link_id', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('order_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created_at', 'biginteger', ['null' => false])
            ->addColumn('paid_at', 'biginteger', ['null' => true])
            ->addIndex(['badge_no'])
            ->addIndex(['order_id'])
            ->addForeignKey('badge_no', 'members', 'badge_no', ['delete' => 'CASCADE'])
            ->create();

        $this->table('site_selection_info', ['id' => false, 'primary_key' => ['badge_no']])
            ->addColumn('badge_no', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('country', 'char', ['limit' => 2, 'null' => false])
            ->addColumn('address', 'text', ['null' => false])
            ->addColumn('wants_paper_publications', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('consent_to_publish_name', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('created_at', 'biginteger', ['null' => false])
            ->addColumn('updated_at', 'biginteger', ['null' => false])
            ->addForeignKey('badge_no', 'members', 'badge_no', ['delete' => 'CASCADE'])
            ->create();

        $this->table('roles')
            ->insert([['name' => 'site-selection']])
            ->saveData();

        $this->table('permissions')
            ->insert(array_map(fn($name) => ['name' => $name], self::PERMISSIONS))
            ->saveData();

        $this->execute("INSERT INTO role_permissions (role_id, permission_id)
            SELECT role_id, permission_id
            FROM roles CROSS JOIN permissions
            WHERE roles.name = 'site-selection' AND permissions.name = 'see-site-selection'");
    }

    public function down(): void
    {
        $names = implode(', ', array_map(fn($name) => "'" . $name . "'", self::PERMISSIONS));
        $this->execute("DELETE FROM role_permissions WHERE permission_id IN (SELECT permission_id FROM permissions WHERE name IN ($names))");
        $this->execute("DELETE FROM role_permissions WHERE role_id IN (SELECT role_id FROM roles WHERE name = 'site-selection')");
        $this->execute("DELETE FROM member_roles WHERE role_id IN (SELECT role_id FROM roles WHERE name = 'site-selection')");
        $this->execute("DELETE FROM permissions WHERE name IN ($names)");
        $this->execute("DELETE FROM roles WHERE name = 'site-selection'");
        $this->table('site_selection_info')->drop()->save();
        $this->table('site_selection_payment_links')->drop()->save();
        $this->table('site_selection_tokens')->drop()->save();
    }
}
