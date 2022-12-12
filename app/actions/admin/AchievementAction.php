<?php

namespace actions\admin;

use helpers\Image;
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
        if ($imageBase64 = $request->getParam('image')) {
            if ($picture = (new Image())->base64ToImage($imageBase64, $this->container->uploadDir . 'achievement/')) {
                $attributes['image'] = $picture;
            }
        }
        $group = new AchievementGroup();
        if ($errors = $this->container->validator->validate($attributes, [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $group->fill($attributes);
        if ($group->save()) {
            return $returnResponse->successResponse([
                'id' => $group->id,
                'name' => $group->name,
                'image' => $group->image,
            ]);
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
            'description' => $request->getParam('description'),
        ];
        if ($imageBase64 = $request->getParam('image')) {
            if ($picture = (new Image())->base64ToImage($imageBase64, $this->container->uploadDir . 'achievement/')) {
                $attributes['image'] = $picture;
            }
        }
        if ($imageBase64 = $request->getParam('icon')) {
            if ($picture = (new Image())->base64ToImage($imageBase64, $this->container->uploadDir . 'achievement/')) {
                $attributes['icon'] = $picture;
            }
        }
        $achievement = new Achievement();
        $rules = [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
            'description' => Validator::stringType()->length(0, 10000),
        ];
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
            return $returnResponse->successResponse([
                'id' => $achievement->id,
            ]);
        }
        return $returnResponse->saveErrorResponse();
    }

    public function deleteGroup(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $group = $this->db->table((new AchievementGroup())->getTable())->where('id', $id)->get()->shift();
        if (empty($group)) {
            return $returnResponse->errorResponse('Группа не существует');
        }
        //TODO проверить токен
        $this->db->table((new AchievementGroup())->getTable())->where('id', $id)->delete();
        return $returnResponse->successResponse();
    }

    public function delete(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $achievement = $this->db->table((new Achievement())->getTable())->where('id', $id)->get()->shift();
        if (empty($achievement)) {
            return $returnResponse->errorResponse('Достижение не существует');
        }
        //TODO проверить токен
        $this->db->table((new Achievement())->getTable())->where('id', $id)->delete();
        return $returnResponse->successResponse();
    }
}