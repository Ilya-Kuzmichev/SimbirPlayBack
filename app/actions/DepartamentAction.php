<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Departament;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DepartamentAction extends Action
{

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $users = $this->db->table((new Departament())->getTable())->get(['id', 'name'])->all();
        return $returnResponse->successResponse($users);
    }
}