<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_achievement extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\AchievementGroup())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
        $table = (new \models\Achievement())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `group_id` int(2),
                `min_price` int(11),
                `max_price` int(11),
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\Achievement())->getTable())
            ->drop();
        $this->table((new \models\AchievementGroup())->getTable())
            ->drop();
    }
}
