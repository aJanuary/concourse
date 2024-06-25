<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class IncreaseUrlLengths extends AbstractMigration
{
    public function up(): void
    {
        $this->table('prog_discord_posts')
            ->changeColumn('post_url', 'string', ['limit' => 2048])
            ->update();
        $this->table('prog_replay')
            ->changeColumn('replay_url', 'string', ['limit' => 2048])
            ->update();
        $this->table('prog_sessions')
            ->changeColumn('rce_url', 'string', ['limit' => 2048])
            ->update();
        $this->table('prog_stages')
            ->changeColumn('viewer_url', 'string', ['limit' => 2048])
            ->changeColumn('participant_url', 'string', ['limit' => 2048])
            ->update();
        $this->table('prog_zoom')
            ->changeColumn('zoom_url', 'string', ['limit' => 512, 'null' => false])
            ->update();
    }
}
