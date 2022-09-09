<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_merch extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Merch())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `price` int(11) NOT NULL,
                `picture` varchar(255),
                `active` boolean DEFAULT 1,
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\Merch())->getTable())
            ->drop();
    }
}
