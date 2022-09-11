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

    public function successResponse(array $result = []): Response
    {
        return $this->returnJSON([
            'success' => true,
            'errors' => [],
            'result' => $result,
        ]);
    }

    public function errorsResponse(array $errors): Response
    {
        return $this->returnJSON([
            'success' => false,
            'errors' => $errors,
        ]);
    }

    public function errorResponse(string $error): Response
    {
        return $this->errorsResponse([$error]);
    }

    public function saveErrorResponse(): Response
    {
        return $this->errorResponse('Ошибка сохранения');
    }

    public static function responseForOptionsRequest()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        exit;
    }

    private function returnJSON(array $data)
    {
        $response = $this->response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', true);
        return $response->withJson($data);
    }
}