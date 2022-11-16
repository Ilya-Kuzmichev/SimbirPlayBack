<?php

namespace actions\admin;

use helpers\ReturnedResponse;
use models\Achievement;
use models\AchievementGroup;
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
        ];
        $achievement = new Achievement();
        if ($errors = $this->container->validator->validate($attributes, [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $achievement->fill($attributes);
        if ($achievement->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}