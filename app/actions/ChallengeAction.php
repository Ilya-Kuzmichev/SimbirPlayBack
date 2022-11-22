<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Challenge;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ChallengeAction extends Action
{

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $challenges = $this->db->table((new Challenge())->getTable())->get()->all();
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
}