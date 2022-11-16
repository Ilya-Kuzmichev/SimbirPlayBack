<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_challenge extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Challenge())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `description` text,
                `start_date` date,
                `end_date` date,
                `budget` int(11),
                `responsible_id` int(11),
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\Challenge())->getTable())
            ->drop();
    }
}
