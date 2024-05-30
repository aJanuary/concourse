<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddProgDiscordPostsIndex extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('prog_discord_posts', ['id' => false, 'primary_key' => ['item_id']]);
        $table->addIndex(['room_id', 'start'])
              ->update();
    }
}
