<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Add_column_to_user extends AbstractMigration
{
    protected function up(): void
    {
        $table = (new \models\User())->getTable();
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN share_achievement boolean DEFAULT 1");
        $this->execute("ALTER TABLE `{$table}` ADD COLUMN share_rating boolean DEFAULT 1");

        $departmentTable = (new \models\Department())->getTable();
        $userTable = (new \models\User())->getTable();
        $departments = [
            1 => 'Backend',
            2 => 'Frontend',
            3 => 'Аккаунтинг',
            4 => 'Аналитика',
            5 => 'Дизайн',
        ];
        foreach ($departments as $departmentId => $departmentName) {
            $this->insert($departmentTable, [
                'id' => $departmentId,
                'name' => $departmentName,
            ]);
        }
        $users = [
            [
                'external_id' => 1,
                'name' => 'Илья',
                'surname' => 'Кузьмичев',
                'patronymic' => 'Алексеевич',
                'login' => 'ilya',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'ilya.kuzmichev@simbirsoft.com',
                'department_id' => 1,
            ],
            [
                'external_id' => 2,
                'name' => 'Артур',
                'surname' => 'Багдасарян',
                'patronymic' => 'Отчество Артуса',
                'login' => 'artur',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'artur.bagdasaryan@simbirsoft.com',
                'department_id' => 3,
            ],
            [
                'external_id' => 3,
                'name' => 'Даниил',
                'surname' => 'Осипов',
                'patronymic' => 'Отчество Даниила',
                'login' => 'daniil',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'daniil.osipov@simbirsoft.com',
                'department_id' => 4,
            ], [
                'external_id' => 4,
                'name' => 'Александра',
                'surname' => 'Двойнина',
                'patronymic' => 'Отчество Александры',
                'login' => 'aleksandra',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'aleksandra.dvoinina@simbirsoft.com',
                'department_id' => 2,
            ], [
                'external_id' => 5,
                'name' => 'Дмитрий',
                'surname' => 'Павловский',
                'patronymic' => 'Отчество Дмитрия',
                'login' => 'dmitrii',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'dmitrii.pavlovskiy@simbirsoft.com',
                'department_id' => 2,
            ], [
                'external_id' => 5,
                'name' => 'Илья',
                'surname' => 'Трифонов',
                'patronymic' => 'Отчество Ильи',
                'login' => 'ilyat',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'ilya.trifonov@simbirsoft.com',
                'department_id' => 5,
            ],
        ];
        foreach ($users as $userData) {
            $this->insert($userTable, $userData);
        }
    }

    protected function down(): void
    {
        $table = (new \models\User())->getTable();
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN share_achievement");
        $this->execute("ALTER TABLE `{$table}` DROP COLUMN share_rating");
    }
}
