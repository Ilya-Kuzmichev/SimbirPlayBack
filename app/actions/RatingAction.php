<?php

namespace actions;

use helpers\ReturnedResponse;
use helpers\Server;
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
        $tableBonus = (new Bonus())->getTable();
        $departmentList = Department::getList();
        $departmentId = $request->getParam('departmentId');
        $where = $departmentId ? ' WHERE department_id = ' . $departmentId . ' ' : '';
        $users = $this->db::select("SELECT u.id, u.name, u.surname, u.department_id, SUM(b.bonus) rating FROM {$tableUser} u INNER JOIN {$tableBonus} b ON u.id = b.user_id {$where} GROUP BY u.id ORDER BY rating DESC");
        foreach ($users as $index => $user) {
            $users[$index] = (array)$user;
            $users[$index]['department'] = $departmentList[(int)$user->department_id] ?? null;
            $users[$index]['avatar'] = (new Server())->getHost() . '/images/user/' . $user->id . '.jpg';
        }
        return $returnResponse->successResponse($users);
    }
}