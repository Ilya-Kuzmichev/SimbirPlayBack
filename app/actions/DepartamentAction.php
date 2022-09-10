<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Departament;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DepartamentAction
{
    private $container;
    private $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container['db'];
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $users = $this->db->table((new Departament())->getTable())->get(['id', 'name'])->all();
        return $returnResponse->successResponse($users);
    }
}