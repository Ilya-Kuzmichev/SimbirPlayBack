<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_slimulus extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Stimulus())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `promo_id` int(11) NOT NULL,
                `giver_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `date` timestamp DEFAULT CURRENT_TIMESTAMP,
                `balls` int(11) NOT NULL,
                `comment` varchar(255),
                PRIMARY KEY (`id`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\Stimulus())->getTable())
            ->drop();
    }
}