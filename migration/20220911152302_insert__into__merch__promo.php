<?php

declare(strict_types=1);

use Phoenix\Migration\AbstractMigration;

final class Insert_into_merch_promo extends AbstractMigration
{
    protected function up(): void
    {
        $merchTable = (new \models\Merch())->getTable();
        $promoTable = (new \models\Promo())->getTable();
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
        $promoList = [
            [
                'name' => 'Посадил дерево или лес',
                'default_rating' => 5,
            ],
            [
                'name' => 'Организовал или помог убрать мусор на природной территории',
                'default_rating' => 8,
            ],
            [
                'name' => 'Организовал раздельное ведение мусора',
                'default_rating' => 8,
            ],
            [
                'name' => 'Сделал ИПР',
                'default_rating' => 5,
            ],
            [
                'name' => 'Сдал квалификацию',
                'default_rating' => 5,
            ],
            [
                'name' => 'Выиграл в профессиональном конкурсе',
                'default_rating' => 5,
            ],
            [
                'name' => 'Получил положительный отзыв от Клиента',
                'default_rating' => 3,
            ],
            [
                'name' => 'Спас Команду и продукт Клиента',
                'default_rating' => 5,
            ],
            [
                'name' => 'Передал референс или информацию для расширения',
                'default_rating' => 5,
            ],
            [
                'name' => 'Организовал помощь приюту для бездомных животных',
                'default_rating' => 8,
            ],
            [
                'name' => 'Организовал помощь социально незащищенной категории',
                'default_rating' => 8,
            ],
            [
                'name' => 'Провел мероприятие с детьми из детских домов',
                'default_rating' => 8,
            ],

        ];
        foreach ($promoList as $promo) {
            $this->insert($promoTable, $promo);
        }
    }

    protected function down(): void
    {
        $this->execute('TRUNCATE TABLE ' . (new \models\Merch())->getTable());
        $this->execute('TRUNCATE TABLE ' . (new \models\Promo())->getTable());
    }
}
