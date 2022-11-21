<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Achievement;
use models\Challenge;
use models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class ChallengeAction extends Action
{

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $challenges = $this->db->table((new Challenge())->getTable())
            ->get(['id', 'name', 'start_date', 'end_date', 'responsible_id'])->all();
        $challengesParse = [];
        foreach ($challenges as $challenge) {
            $challengesParse[] = $this->formatChallenge($challenge);
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
        return $returnResponse->successResponse($this->formatChallenge($challenge));
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