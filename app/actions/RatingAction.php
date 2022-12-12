<?php

namespace actions;

use helpers\ReturnedResponse;
use helpers\Server;
use models\Achievement;
use models\AchievementToChallenge;
use models\Bonus;
use models\Department;
use models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RatingAction extends Action
{
    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $tableUser = (new User())->getTable();
        $tableAchievement = (new Achievement())->getTable();
        $tableAchievementChallenge = (new AchievementToChallenge())->getTable();
        $tableBonus = (new Bonus())->getTable();
        $departmentList = Department::getList();
        $where = ' u.share_rating = 1 ';
        $sql = "SELECT u.id, u.name, u.surname, u.patronymic, u.department_id as departmentId, SUM(b.bonus) rating
            FROM {$tableUser} u INNER JOIN {$tableBonus} b ON u.id = b.user_id";
        if ($challengeId = $request->getParam('challengeId')) {
            $sql .= " JOIN {$tableAchievement} a ON a.id = b.achievement_id";
            $sql .= " JOIN {$tableAchievementChallenge} ac ON a.id = ac.achievement_id";
            $where .= ' AND ac.challenge_id = ' . $challengeId;
        }
        if ($departmentId = $request->getParam('departmentId')) {
            $where .= ' AND  u.department_id = ' . $departmentId;
        }
        $users = $this->db::select($sql . " WHERE {$where} GROUP BY u.id ORDER BY rating DESC");
        $position = 1;
        foreach ($users as $index => $user) {
            $users[$index] = (array)$user;
            $users[$index]['department'] = $departmentList[(int)$user->departmentId] ?? null;
            $users[$index]['avatar'] = (new Server())->getHost() . '/images/user/' . $user->id . '.jpg';
            $users[$index]['position'] = $position++;
        }
        return $returnResponse->successResponse($users);
    }
}