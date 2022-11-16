<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Add_user_table_columns extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\User())->getTable();
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN role_id INT(2) DEFAULT 1");
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN token varchar(255)");
    }

    protected function down(): void
    {
        $table = (new \models\User())->getTable();
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN role_id");
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN token");
    }
}
