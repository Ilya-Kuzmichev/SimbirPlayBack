<?php

namespace helpers;

use Slim\Http\Response;

class ReturnedResponse
{
    private $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function successResponse(array $data = []): Response
    {
        $data['success'] = true;
        $data['errors'] = [];
        return $this->response->withJson($data);
    }

    public function errorsResponse(array $errors): Response
    {
        $data['success'] = true;
        $data['errors'] = $errors;
        return $this->response->withJson($data);
    }

    public function errorResponse(string $error): Response
    {
        return $this->errorsResponse([$error]);
    }

    public function saveErrorResponse(): Response
    {
        return $this->errorResponse('Ошибка сохранения');
    }
}