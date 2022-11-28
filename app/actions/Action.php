<?php

namespace actions;

use helpers\Server;
use models\Achievement;
use models\AchievementGroup;
use models\AchievementToChallenge;
use models\Challenge;
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
        $achievementIds = $this->db->table((new AchievementToChallenge())->getTable())
            ->get()->where('challenge_id', $challenge->id)->all();
        foreach ($achievementIds as $achievementIdsRow) {
            $achievement = $this->db->table((new Achievement())->getTable())
                ->get()->where('id', $achievementIdsRow->achievement_id)->shift();
            if ($achievement) {
                $achievementParse[] = [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'min' => $achievement->min_price,
                    'max' => $achievement->max_price,
                ];
            }
        }
        return [
            'id' => $challenge->id,
            'name' => $challenge->name,
            'description' => $challenge->description,
            'startDate' => date('d.m.Y', strtotime($challenge->start_date)),
            'endDate' => date('d.m.Y', strtotime($challenge->end_date)),
            //TODO
            'achievements' => $achievementParse,
            'balance' => 100,
            'department' => 'Направление',
            'image' => $challenge->image ? (new Server())->getHost() . '/images/challenge/' . $challenge->image : '',
            'responsible' => $responsible ? $responsible->name : null,
        ];
    }

    protected function formatAchievement($achievement)
    {
        $group = $this->db->table((new AchievementGroup())->getTable())
            ->get()->where('id', $achievement->group_id)->shift();
        $challengeParse = [];
        $challengeIds = $this->db->table((new AchievementToChallenge())->getTable())
            ->get()->where('achievement_id', $achievement->id)->all();
        foreach ($challengeIds as $challengeIdsRow) {
            $challenge = $this->db->table((new Challenge())->getTable())->get()
                ->where('id', $challengeIdsRow->challenge_id)->shift();
            if ($challenge) {
                $challengeParse[] = [
                    'id' => $challenge->id,
                    'name' => $challenge->name,
                    'description' => $challenge->description,
                    'startDate' => date('d.m.Y', strtotime($challenge->start_date)),
                    'endDate' => date('d.m.Y', strtotime($challenge->end_date)),
                    'balance' => 100,
                    'image' => $challenge->image ? (new Server())->getHost() . '/images/challenge/' . $challenge->image : '',
                ];
            }
        }
        return [
            'id' => $achievement->id,
            'name' => $achievement->name,
            'groupId' => $achievement->group_id ?: '',
            'group' => $group ? $group->name : '',
            'min' => $achievement->min_price ?: '',
            'max' => $achievement->max_price ?: '',
            'challenges' => $challengeParse,
            'image' => $achievement->image ? (new Server())->getHost() . '/images/achievement/' . $achievement->image : '',
        ];
    }
}