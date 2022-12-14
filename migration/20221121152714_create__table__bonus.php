<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_bonus extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Bonus())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `achievement_id` int(11) NOT NULL,
                `responsible_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `date` timestamp DEFAULT CURRENT_TIMESTAMP,
                `bonus` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\Bonus())->getTable())
            ->drop();
    }
}