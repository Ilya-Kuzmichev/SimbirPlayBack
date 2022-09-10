<?php

namespace actions;

use helpers\ReturnedResponse;
use helpers\Server;
use models\Departament;
use models\Stimulus;
use models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Uri;

class RatingAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $tableUser = (new User())->getTable();
        $tableStimulus = (new Stimulus())->getTable();
        $departamentList = Departament::getList();
        $departamentId = $request->getParam('departamentId');
        $where = $departamentId ? ' WHERE departament_id = ' . $departamentId . ' ' : '';
        $users = $this->container['db']::select("SELECT u.id, u.name, u.surname, u.departament_id, SUM(s.balls) rating FROM {$tableUser} u INNER JOIN {$tableStimulus} s ON u.id = s.user_id {$where} GROUP BY u.id ORDER BY rating DESC");
        foreach ($users as $index => $user) {
            $users[$index] = (array)$user;
            $users[$index]['departament'] = $departamentList[(int)$user->departament_id] ?? null;
            $users[$index]['avatar'] = (new Server())->getHost() . '/images/user/' . $user->id . '.jpg';
        }
        return $returnResponse->successResponse($users);
    }
}