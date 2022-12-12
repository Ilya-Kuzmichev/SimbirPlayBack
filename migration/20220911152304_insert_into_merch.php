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
                'name' => 'Наушники',
                'price' => 30,
            ],
            [
                'name' => 'Стикеры',
                'price' => 5,
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
