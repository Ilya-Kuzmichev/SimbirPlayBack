<?php

namespace actions;

use models\Departament;
use models\Stimulus;
use models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RatingAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function list(Request $request, Response $response, $args)
    {
        $tableUser = (new User())->getTable();
        $tableStimulus = (new Stimulus())->getTable();
        $departamentList = Departament::getList();
        $users = $this->container['db']::select("SELECT u.id, u.name, u.surname, u.departament_id, SUM(s.balls) rating FROM {$tableUser} u INNER JOIN {$tableStimulus} s ON u.id = s.user_id GROUP BY u.id");
        foreach ($users as $index => $user) {
            $users[$index] = (array)$user;
            $users[$index]['departament'] = $departamentList[(int)$user->departament_id] ?? null;
        }
        return $response->withJson($users);
    }
}