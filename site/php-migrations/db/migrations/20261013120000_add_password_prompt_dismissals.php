<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPasswordPromptDismissals extends AbstractMigration
{
    public function up(): void
    {
      // Keyed by email like password_auth, as a password covers every member on an email
      $this->table('password_prompt_dismissals')
           ->addColumn('email', 'string', ['limit' => 100, 'null' => false])
           ->addIndex(['email'], ['unique' => true, 'name' => 'password_prompt_dismissals_email'])
           ->create();
    }

    public function down(): void
    {
      $this->table('password_prompt_dismissals')->drop()->save();
    }
}
