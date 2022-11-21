<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Department;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DepartmentAction extends Action
{
    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $users = $this->db->table((new Department())->getTable())->get(['id', 'name'])->all();
        return $returnResponse->successResponse($users);
    }
}