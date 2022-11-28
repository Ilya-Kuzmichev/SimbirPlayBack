<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_achievement_to_challenge extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\AchievementToChallenge())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `challenge_id` int(11) NOT NULL,
                `achievement_id` int(11) NOT NULL
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\AchievementToChallenge())->getTable())
            ->drop();
    }
}
