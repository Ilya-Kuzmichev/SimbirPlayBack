<?php
declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Add__column__icon extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Achievement())->getTable();
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN icon VARCHAR(255)");
    }

    protected function down(): void
    {
        $table = (new \models\Achievement())->getTable();
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN icon");
    }
}
