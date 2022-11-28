<?php
declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Add_column_image extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\Achievement())->getTable();
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN image VARCHAR(255)");
        $table = (new \models\AchievementGroup())->getTable();
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN image VARCHAR(255)");
        $table = (new \models\Challenge())->getTable();
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN image VARCHAR(255)");
    }

    protected function down(): void
    {
        $table = (new \models\Achievement())->getTable();
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN image");
        $table = (new \models\AchievementGroup())->getTable();
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN image");
        $table = (new \models\Challenge())->getTable();
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN image");
    }
}
