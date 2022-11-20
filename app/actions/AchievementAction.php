<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Achievement;
use models\AchievementGroup;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AchievementAction extends Action
{

    public function groupList(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $groups = $this->db->table((new AchievementGroup())->getTable())->get(['id', 'name'])->all();
        return $returnResponse->successResponse($groups);
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $achievementParse = [];
        $achievements = $this->db->table((new Achievement())->getTable())
            ->get(['id', 'name', 'challenge_id', 'group_id', 'min_price', 'max_price'])->all();
        foreach ($achievements as $achievement) {
            if ($achievement->challenge_id) {
                continue;
            }
            $group = $this->db->table((new AchievementGroup())->getTable())
                ->get()->where('id', $achievement->group_id)->shift();
            $achievementParse[] = [
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
        return $returnResponse->successResponse($achievementParse);
    }
}
