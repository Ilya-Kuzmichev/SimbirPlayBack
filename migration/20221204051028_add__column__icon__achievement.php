<?php
declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Add_column_icon_achievement extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Achievement())->getTable();
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN description VARCHAR(255)");
    }

    protected function down(): void
    {
        $table = (new \models\Achievement())->getTable();
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN description");
    }
}
