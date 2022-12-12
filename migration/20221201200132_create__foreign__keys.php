<?php

declare(strict_types=1);

use Phoenix\Database\Element\ForeignKey;
use Phoenix\Migration\AbstractMigration;

final class Create_foreign_keys extends AbstractMigration
{
    protected function up(): void
    {
        $tableChallenge = (new \models\Challenge())->getTable();
        $tableAchievement = (new \models\Achievement())->getTable();
        $tableDepartment = (new \models\Department())->getTable();
        $this->table((new \models\ChallengeDepartment())->getTable())
            ->addForeignKey('challenge_id', $tableChallenge, 'id', ForeignKey::CASCADE);
        $this->table((new \models\ChallengeDepartment())->getTable())
            ->addForeignKey('department_id', $tableDepartment, 'id', ForeignKey::CASCADE);
        $this->table((new \models\AchievementToChallenge())->getTable())
            ->addForeignKey('achievement_id', $tableAchievement, 'id', ForeignKey::CASCADE);
        $this->table((new \models\AchievementToChallenge())->getTable())
            ->addForeignKey('challenge_id', $tableChallenge, 'id', ForeignKey::CASCADE);
    }

    protected function down(): void
    {
        $this->table((new \models\ChallengeDepartment())->getTable())
            ->dropForeignKey('challenge_id');
        $this->table((new \models\ChallengeDepartment())->getTable())
            ->dropForeignKey('department_id');
        $this->table((new \models\AchievementToChallenge())->getTable())
            ->dropForeignKey('achievement_id');
        $this->table((new \models\AchievementToChallenge())->getTable())
            ->dropForeignKey('challenge_id');
    }
}
