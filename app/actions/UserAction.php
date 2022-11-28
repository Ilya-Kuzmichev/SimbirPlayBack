<?php

namespace actions;

use helpers\ReturnedResponse;
use helpers\Server;
use models\Achievement;
use models\AchievementToChallenge;
use models\Bonus;
use models\Challenge;
use models\Department;
use models\Merch;
use models\Purchases;
use models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserAction extends Action
{

    public function authentication(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $login = $request->getParam('login');
        $password = $request->getParam('password');
        $user = $this->db->table((new User())->getTable())->where('login', $login)
            ->get(['id', 'name', 'surname', 'password', 'role_id', 'token', 'share_achievement', 'share_rating'])
            ->shift();
        if (empty($user)) {
            return $returnResponse->errorResponse('Неправильный логин или пароль');
        }
        if (password_verify($password, $user->password) !== true) {
            return $returnResponse->errorResponse('Неправильный логин или пароль');
        }
        return $returnResponse->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'token' => $user->token,
            'isAdmin' => $user->role_id == User::ROLE_ADMIN,
            'shareAchievement' => (boolean)$user->share_achievement,
            'shareRating' => (boolean)$user->share_rating,
        ]);
    }

    public function search(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $departmentList = Department::getList();
        $users = $this->db->table((new User())->getTable());
        if ($name = $request->getParam('name')) {
            $users = $users->where(function ($query) use ($name) {
                $query->where('name', 'LIKE', "{$name}%")->orWhere('surname', 'LIKE', "{$name}%");
            });
        }
        if ($departmentId = $request->getParam('departmentId')) {
            $users = $users->where('department_id', $departmentId);
        }
        $users = $users->get(['id', 'name', 'surname', 'patronymic', 'department_id as departmentId'])->all();
        foreach ($users as $index => $user) {
            $users[$index] = (array)$user;
            $users[$index]['department'] = $departmentList[(int)$user->departmentId] ?? $departmentList[1];
        }
        return $returnResponse->successResponse($users);
    }

    public function info(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $user = $this->db->table((new User())->getTable())->where('id', $id)->get()->shift();
        if (empty($user)) {
            return $returnResponse->errorResponse('Пользователь не существует');
        }
        $challengeParse = $achievementParse = [];
        $challenges = $this->db->table((new Challenge())->getTable())->get()->where('responsible_id', $id)->all();
        foreach ($challenges as $challenge) {
            $challengeParse[] = $this->formatChallenge($challenge);
        }
        $tableAchievement = (new Achievement())->getTable();
        $tableBonus = (new Bonus())->getTable();
        $tableChallengeAchievement = (new AchievementToChallenge())->getTable();
        $tableChallenge = (new Challenge())->getTable();
        $achievementParse = $this->db::select("SELECT a.id, b.bonus, b.date, a.name,
       (SELECT c.name FROM {$tableChallenge} c WHERE c.id = ca.challenge_id) as challenge
                FROM {$tableBonus} b
                    INNER JOIN {$tableAchievement} a ON b.achievement_id = a.id
                    JOIN {$tableChallengeAchievement} ca ON ca.achievement_id = a.id
                WHERE b.user_id = " . $id);
        $bonus = $this->db::select("SELECT SUM(bonus) bonus FROM {$tableBonus} WHERE user_id = {$id}");
        $rating = $this->db::select("SELECT SUM(bonus) bonus FROM {$tableBonus} WHERE user_id = {$id} AND bonus > 0");
        return $returnResponse->successResponse([
            'name' => $user->name,
            'surname' => $user->surname,
            'avatar' => (new Server())->getHost() . '/images/user/' . $user->id . '.jpg',
            'rating' => $rating ? array_shift($rating)->bonus : 0,
            'bonus' => $bonus ? array_shift($bonus)->bonus : 0,
            'achievements' => $achievementParse,
            'challenges' => $challengeParse,
            'shareAchievement' => (boolean)$user->share_achievement,
            'shareRating' => (boolean)$user->share_rating,
        ]);
    }

    public function update(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        //TODO переделать на токен
        $id = $args['id'] ?? null;
        $user = $this->db->table((new User())->getTable())
            ->get()->where('id', $id)->shift();
        if (empty($user)) {
            return $returnResponse->errorResponse('Пользователь не существует');
        }
        $attributes = [];
        $shareAchievement = $request->getParam('shareAchievement');
        $shareRating = $request->getParam('shareRating');
        if ($shareAchievement !== null) {
            $attributes['share_achievement'] = $shareAchievement ? 1 : 0;
        }
        if ($shareRating !== null) {
            $attributes['share_rating'] = $shareRating ? 1 : 0;
        }
        if ($this->db->table((new User())->getTable())->where('id', $id)->update($attributes)) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}