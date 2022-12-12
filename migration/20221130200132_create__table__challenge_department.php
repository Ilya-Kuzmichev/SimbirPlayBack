<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_table_challenge_department extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\ChallengeDepartment())->getTable();
        $this->execute("CREATE TABLE `{$table}` (
                `challenge_id` int(11) NOT NULL,
                `department_id` int(11) NOT NULL
            ) ENGINE=innoDB DEFAULT CHARSET=utf8"
        );
    }

    protected function down(): void
    {
        $this->table((new \models\ChallengeDepartment())->getTable())
            ->drop();
    }
}
