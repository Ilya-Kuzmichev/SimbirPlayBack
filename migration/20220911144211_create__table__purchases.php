<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_purchases extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Purchases())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `merch_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `price` int(11) NOT NULL,
                `address` varchar(255),
                `date_send` datetime,
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\Purchases())->getTable())
            ->drop();
    }
}
