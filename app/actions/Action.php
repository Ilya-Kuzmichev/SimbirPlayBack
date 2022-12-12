<?php

namespace actions;

use helpers\Server;
use models\Achievement;
use models\AchievementGroup;
use models\AchievementToChallenge;
use models\Bonus;
use models\Challenge;
use models\ChallengeDepartment;
use models\Department;
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
        $tableBonus = (new Bonus())->getTable();
        $responsible = $this->db->table((new User())->getTable())
            ->get()->where('id', $challenge->responsible_id)->shift();
        $achievementParse = $departmentParse = [];
        $spentBalance = 0;
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
                $bonusRow = $this->db::select("SELECT SUM(bonus) bonus FROM {$tableBonus} WHERE achievement_id = {$achievement->id}");
                $spentBalance += $bonusRow ? array_shift($bonusRow)->bonus : 0;
            }
        }
        $departmentIds = $this->db->table((new ChallengeDepartment())->getTable())
            ->get()->where('challenge_id', $challenge->id)->all();
        foreach ($departmentIds as $departmentIdsRow) {
            $department = $this->db->table((new Department())->getTable())
                ->get()->where('id', $departmentIdsRow->department_id)->shift();
            if ($department) {
                $departmentParse[] = $department->name;
            }
        }
        return [
            'id' => $challenge->id,
            'name' => $challenge->name,
            'description' => $challenge->description,
            'startDate' => date('d.m.Y', strtotime($challenge->start_date)),
            'endDate' => date('d.m.Y', strtotime($challenge->end_date)),
            'achievements' => $achievementParse,
            'balance' => $challenge->budget - $spentBalance,
            'department' => implode(', ', $departmentParse),
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
            'description' => $achievement->description,
            'groupId' => $achievement->group_id ?: '',
            'group' => $group ? $group->name : '',
            'min' => $achievement->min_price ?: '',
            'max' => $achievement->max_price ?: '',
            'challenges' => $challengeParse,
            'image' => $achievement->image ? (new Server())->getHost() . '/images/achievement/' . $achievement->image : '',
            'icon' => $achievement->icon ? (new Server())->getHost() . '/images/achievement/' . $achievement->icon : '',
        ];
    }
}