<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Promo;
use models\Stimulus;
use Respect\Validation\Validator;
use models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserAction
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
        $users = $this->db->table((new User())->getTable())->get(['id', 'name', 'surname'])->all();
        return $response->withJson($users);
    }

    public function createStimulus(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $userId = $request->getParam('userId');
        $giverId = $request->getParam('giverId');
        $promoId = $request->getParam('promoId');
        if (!$this->db->table((new User())->getTable())->where('id', $userId)->get()->shift()) {
            return $returnResponse->errorResponse('Такого пользователя не существует');
        }
        if (!$this->db->table((new User())->getTable())->where('id', $giverId)->get()->shift()) {
            return $returnResponse->errorResponse('Такого пользователя не существует');
        }
        if (!$this->db->table((new Promo())->getTable())->where('id', $promoId)->get()->shift()) {
            return $returnResponse->errorResponse('Такого поощрения не существует');
        }
        $attributes = [
            'user_id' => $userId,
            'promo_id' => $promoId,
            'giver_id' => $promoId,
            'balls' => $request->getParam('balls'),
        ];
        $stimulus = new Stimulus();
        if ($errors = $this->container->validator->validate($attributes, [
            'balls' => Validator::noWhitespace()->intVal()->between(0, 10000),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $stimulus->fill($attributes);
        if ($stimulus->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}