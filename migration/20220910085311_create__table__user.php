<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_user extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\User())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `external_id` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                `surname` varchar(255) NOT NULL,
                `login` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `email` varchar(255),
                `active` boolean DEFAULT 1,
                `departament_id` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\User())->getTable())
            ->drop();
    }
}