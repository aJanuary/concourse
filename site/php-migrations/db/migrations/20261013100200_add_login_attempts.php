<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddLoginAttempts extends AbstractMigration
{
    public function up(): void
    {
      // subject is a hash of the email or client address, so no personal data is kept
      $this->table('login_attempts')
           ->addColumn('bucket', 'string', ['limit' => 30, 'null' => false])
           ->addColumn('subject', 'string', ['limit' => 64, 'null' => false])
           ->addColumn('created_at', 'timestamp', ['null' => false])
           ->addIndex(['bucket', 'subject', 'created_at'])
           ->create();
    }

    public function down(): void
    {
      $this->table('login_attempts')->drop()->save();
    }
}
