<?php

namespace actions;

use models\Achievement;
use models\AchievementGroup;
use models\User;
use Psr\Container\ContainerInterface;

class Action
{
    protected $container;
    protected $validator;
    protected $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->db;
        $this->validator = $container->validator;
    }

    protected function formatChallenge($challenge)
    {
        $responsible = $this->db->table((new User())->getTable())
            ->get()->where('id', $challenge->responsible_id)->shift();
        $achievementParse = [];
        $achievements = $this->db->table((new Achievement())->getTable())
            ->get()->where('challenge_id', $challenge->id)->all();
        foreach ($achievements as $achievement) {
            $achievementParse[] = [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'min' => $achievement->min_price,
                'max' => $achievement->max_price,
            ];
        }
        return [
            'name' => $challenge->name,
            'description' => $challenge->description,
            'startDate' => date('d.m.Y', strtotime($challenge->start_date)),
            'endDate' => date('d.m.Y', strtotime($challenge->end_date)),
            //TODO
            'achievements' => $achievementParse,
            'balance' => 100,
            'department' => 'Направление',
            'icon' => 'https://cdnn21.img.ria.ru/images/07e4/0a/1a/1581598772_0:489:2000:1614_1920x0_80_0_0_63a9806e0d87c17481dc578721e4e99d.jpg.webp',
            'responsible' => $responsible ? $responsible->name : null,
        ];
    }

    protected function formatAchievement($achievement)
    {
        $group = $this->db->table((new AchievementGroup())->getTable())
            ->get()->where('id', $achievement->group_id)->shift();
        return [
            'id' => $achievement->id,
            'name' => $achievement->name,
            'groupId' => $achievement->group_id ?: '',
            'group' => $group ? $group->name : '',
            'min' => $achievement->min_price ?: '',
            'max' => $achievement->max_price ?: '',
            //TODO
            'icon' => 'https://www.awicons.com/stock-icons/symbol-black/preview/gallery.png',
        ];
    }
}