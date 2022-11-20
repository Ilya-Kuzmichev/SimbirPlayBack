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
            $responsible = $this->db->table((new User())->getTable())
                ->get()->where('id', $challenge->responsible_id)->shift();
            $challengesParse[] = [
                'id' => $challenge->id,
                'name' => $challenge->name,
                'startDate' => date('d.m.Y', strtotime($challenge->start_date)),
                'endDate' => date('d.m.Y', strtotime($challenge->end_date)),
                //TODO
                'achievements' => 'Список достижений',
                'balance' => 100,
                'icon' => 'https://cdnn21.img.ria.ru/images/07e4/0a/1a/1581598772_0:489:2000:1614_1920x0_80_0_0_63a9806e0d87c17481dc578721e4e99d.jpg.webp',
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
        $achievementParse = [];
        $achievements = $this->db->table((new Achievement())->getTable())
            ->get()->where('challenge_id', $challenge->id)->all();
        foreach ($achievements as $achievement) {
            $achievementParse[] = [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'min' => $achievement->min_price,
                'max' => $achievement->max_price,
            ];
        }
        return $returnResponse->successResponse([
            'name' => $challenge->name,
            'description' => $challenge->description,
            'startDate' => date('d.m.Y', strtotime($challenge->start_date)),
            'endDate' => date('d.m.Y', strtotime($challenge->end_date)),
            //TODO
            'achievements' => $achievementParse,
            'balance' => 100,
            'department' => 'Направление',
            'icon' => 'https://cdnn21.img.ria.ru/images/07e4/0a/1a/1581598772_0:489:2000:1614_1920x0_80_0_0_63a9806e0d87c17481dc578721e4e99d.jpg.webp',
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