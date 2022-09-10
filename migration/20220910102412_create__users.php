<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Create_users extends AbstractMigration
{
    protected function up(): void
    {
        $departamentTable = (new \models\Departament())->getTable();
        $userTable = (new \models\User())->getTable();
        $departaments = [
            1 => 'Backend',
            2 => 'Frontend',
            3 => 'Аккаунтинг',
            4 => 'Аналитика',
        ];
        foreach ($departaments as $departamentId => $departamentName) {
            $this->insert($departamentTable, [
                'id' => $departamentId,
                'name' => $departamentName,
            ]);
        }
        $users = [
            [
                'external_id' => 1,
                'name' => 'Илья',
                'surname' => 'Кузьмичев',
                'login' => 'ilya',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'ilya.kuzmichev@simbirsoft.com',
                'departament_id' => 1,
            ],
            [
                'external_id' => 2,
                'name' => 'Артур',
                'surname' => 'Багдасарян',
                'login' => 'artur',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'artur.bagdasaryan@simbirsoft.com',
                'departament_id' => 3,
            ],
            [
                'external_id' => 3,
                'name' => 'Даниил',
                'surname' => 'Осипов',
                'login' => 'daniil',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'daniil.osipov@simbirsoft.com',
                'departament_id' => 4,
            ],            [
                'external_id' => 4,
                'name' => 'Александра',
                'surname' => 'Двойнина',
                'login' => 'aleksandra',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'aleksandra.dvoinina@simbirsoft.com',
                'departament_id' => 2,
            ],            [
                'external_id' => 5,
                'name' => 'Дмитрий',
                'surname' => 'Павловский',
                'login' => 'dmitrii',
                'password' => password_hash('123123', PASSWORD_BCRYPT, ['cost' => 12]),
                'email' => 'dmitrii.pavlovskiy@simbirsoft.com',
                'departament_id' => 2,
            ],
        ];
        foreach ($users as $userData) {
            $this->insert($userTable, $userData);
        }
    }

    protected function down(): void
    {
        $this->execute('TRUNCATE TABLE ' . (new \models\User())->getTable());
        $this->execute('TRUNCATE TABLE ' . (new \models\Departament())->getTable());
    }
}
