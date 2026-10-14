<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMembers extends AbstractMigration
{
    public function up(): void
    {
      $this->table('members', ['id' => false, 'primary_key' => ['badge_no']])
           ->addColumn('badge_no', 'string', ['limit' => 5, 'null' => false])
           ->addColumn('email', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('name', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addIndex(['email'], ['unique' => true, 'name' => 'members_email'])
           ->create();

      $this->table('sessions', ['id' => false, 'primary_key' => ['session_id']])
           ->addColumn('session_id', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('badge_no', 'string', ['limit' => 5, 'null' => false])
           ->addColumn('expires_at', 'timestamp', ['null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addForeignKey('badge_no', 'members', 'badge_no')
           ->create();

      $this->table('login_links', ['id' => false, 'primary_key' => ['login_code']])
           ->addColumn('login_code', 'string', ['limit' => 100, 'null' => false])
           ->addColumn('badge_no', 'string', ['limit' => 5, 'null' => false])
           ->addColumn('expires_at', 'timestamp', ['null' => false])
           ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => true])
           ->addForeignKey('badge_no', 'members', 'badge_no')
           ->create();
    }

    public function down(): void
    {
      $this->table('login_links')->drop()->save();
      $this->table('sessions')->drop()->save();
      $this->table('members')->drop()->save();
    }
}
