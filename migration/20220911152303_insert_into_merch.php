<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Insert_into_merch extends AbstractMigration
{
    protected function up(): void
    {
        $merchTable = (new \models\Merch())->getTable();
        $merchList = [
            [
                'name' => 'Майка',
                'price' => 20,
            ],
            [
                'name' => 'Толстовка',
                'price' => 30,
            ],
            [
                'name' => 'Чашка',
                'price' => 5,
            ],
            [
                'name' => 'Рюкзак',
                'price' => 15,
            ],
            [
                'name' => 'Бутылка',
                'price' => 10,
            ],
            [
                'name' => 'Power bank',
                'price' => 12,
            ],
        ];
        foreach ($merchList as $merch) {
            $this->insert($merchTable, $merch);
        }
    }

    protected function down(): void
    {
        $this->execute('TRUNCATE TABLE ' . (new \models\Merch())->getTable());
    }
}
