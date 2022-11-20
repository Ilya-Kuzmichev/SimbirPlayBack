<?php

namespace actions\admin;

use helpers\ReturnedResponse;
use models\Achievement;
use models\AchievementGroup;
use models\Challenge;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class AchievementAction extends AdminAction
{
    public function createGroup(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = [
            'name' => $request->getParam('name'),
        ];
        $group = new AchievementGroup();
        if ($errors = $this->container->validator->validate($attributes, [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $group->fill($attributes);
        if ($group->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }

    public function create(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = [
            'name' => $request->getParam('name'),
            'min_price' => $request->getParam('min'),
            'max_price' => $request->getParam('max'),
        ];
        $achievement = new Achievement();
        $rules = [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
        ];
        if ($challengeId = $request->getParam('challengeId')) {
            $challenge = $this->db->table((new Challenge())->getTable())
                ->get()->where('id', $challengeId)->shift();
            if (!$challenge) {
                return $returnResponse->errorsResponse(['Челленджа не существует']);
            }
            $rules['challenge_id'] = Validator::noWhitespace()->intVal();
            $attributes['challenge_id'] = $challengeId;
        }
        if ($groupId = $request->getParam('groupId')) {
            $group = $this->db->table((new AchievementGroup())->getTable())
                ->get()->where('id', $groupId)->shift();
            if (!$group) {
                return $returnResponse->errorsResponse(['Группы не существует']);
            }
            $rules['group_id'] = Validator::noWhitespace()->intVal();
            $attributes['group_id'] = $groupId;
        }
        if ($errors = $this->validator->validate($attributes, $rules)) {
            return $returnResponse->errorsResponse($errors);
        }
        $achievement->fill($attributes);
        if ($achievement->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}