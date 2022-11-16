<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Challenge;
use models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class ChallengeAction
{
    private $container;
    private $db;
    private $validator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->db;
        $this->validator = $container->validator;
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $challenges = $this->db->table((new Challenge())->getTable())
            ->get(['id', 'name', 'start_date', 'end_date', 'responsible_id'])->all();
        $challengesParse = [];
        foreach ($challenges as $challenge) {
            $responsible = $this->db->table((new User())->getTable())
                ->get()->where('id', $challenge->responsible_id)->shift();
            $challengesParse[] = [
                'id' => $challenge->id,
                'name' => $challenge->name,
                'startDate' => $challenge->start_date,
                'endDate' => $challenge->end_date,
                'responsible' => $responsible ? $responsible->name : null,
            ];
        }
        return $returnResponse->successResponse($challengesParse);
    }

    public function info(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $challenge = $this->db->table((new Challenge())->getTable())->where('id', $id)->get()->shift();
        if (empty($challenge)) {
            return $returnResponse->errorResponse('Такого челленджа не существует');
        }
        $responsible = $this->db->table((new User())->getTable())
            ->get()->where('id', $challenge->responsible_id)->shift();
        return $returnResponse->successResponse([
            'name' => $challenge->name,
            'description' => $challenge->description,
            'startDate' => $challenge->start_date,
            'endDate' => $challenge->end_date,
            'budget' => $challenge->budget,
            'responsible' => $responsible ? $responsible->name : null,
        ]);
    }

    public function create(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = [
            'name' => $request->getParam('name'),
            'description' => $request->getParam('description'),
            'start_date' => $request->getParam('startDate'),
            'end_date' => $request->getParam('endDate'),
            'budget' => $request->getParam('budget'),
            'responsible_id' => $request->getParam('responsible'),
        ];
        $challenge = new Challenge();
        if ($errors = $this->validator->validate($attributes, [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
            'description' => Validator::stringType()->length(0, 10000),
            'start_date' => Validator::noWhitespace()->date(),
            'end_date' => Validator::noWhitespace()->date(),
            'budget' => Validator::noWhitespace()->intVal()->between(0, 10000),
            'responsible_id' => Validator::noWhitespace()->intVal(),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $challenge->fill($attributes);
        if ($challenge->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}