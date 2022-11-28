<?php

namespace actions;

use helpers\ReturnedResponse;
use helpers\Server;
use models\Achievement;
use models\AchievementGroup;
use models\Bonus;
use models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class AchievementAction extends Action
{

    public function groupList(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $groups = $this->db->table((new AchievementGroup())->getTable())->get(['id', 'name', 'image'])->all();
        foreach ($groups as $index => $group) {
            $groups[$index]->image = $groups[$index]->image ? (new Server())->getHost() . '/images/achievement/' . $groups[$index]->image : '';
        }
        return $returnResponse->successResponse($groups);
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $achievementParse = [];
        $achievements = $this->db->table((new Achievement())->getTable())
            ->get(['id', 'name', 'group_id', 'min_price', 'max_price'])->all();
        foreach ($achievements as $achievement) {
            $achievementParse[] = $this->formatAchievement($achievement);
        }
        return $returnResponse->successResponse($achievementParse);
    }

    public function info(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $achievement = $this->db->table((new Achievement())->getTable())->where('id', $id)->get()->shift();
        if (empty($achievement)) {
            return $returnResponse->errorResponse('Такого достижения не существует');
        }
        return $returnResponse->successResponse($this->formatAchievement($achievement));
    }

    public function accrueBonuses(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = $rules = [];
        $achievementId = $request->getParam('achievementId');
        $achievement = $this->db->table((new Achievement())->getTable())
            ->get()->where('id', $achievementId)->shift();
        if (!$achievement) {
            return $returnResponse->errorsResponse(['Достижение не найдено']);
        }
        //TODO сделать проверку токена
        $rules['achievement_id'] = Validator::noWhitespace()->intVal();
        $attributes['achievement_id'] = $achievementId;
        $attributes['sum'] = $request->getParam('sum');
        if ($achievement->min_price) {
            $rules['sum'] = Validator::noWhitespace()->intVal()->min($achievement->min_price);
            if ($achievement->max_price) {
                $rules['sum'] = $rules['sum']->max($achievement->max_price);
            } else {
                $rules['sum'] = $rules['sum']->max($achievement->min_price);
            }
        }
        $userId = $request->getParam('userId');
        $user = $this->db->table((new User())->getTable())
            ->get()->where('id', $userId)->shift();
        if (!$user) {
            return $returnResponse->errorsResponse(['Пользователь не найден']);
        }
        $rules['user_id'] = Validator::noWhitespace()->intVal();
        $attributes['user_id'] = $userId;
        if ($errors = $this->validator->validate($attributes, $rules)) {
            return $returnResponse->errorsResponse($errors);
        }
        $bonus = new Bonus();
        $bonus->fill($attributes);
        return $bonus->save()
            ? $returnResponse->successResponse()
            : $returnResponse->saveErrorResponse();
    }
}
