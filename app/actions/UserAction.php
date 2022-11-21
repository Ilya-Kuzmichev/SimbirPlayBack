<?php

namespace actions;

use helpers\ReturnedResponse;
use helpers\Server;
use models\Achievement;
use models\Challenge;
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
            ->get(['id', 'name', 'surname', 'password', 'role_id', 'token'])->shift();
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
        ]);
    }

    public function search(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $name = $request->getParam('name');
        if (mb_strlen($name, 'UTF-8') < 3) {
            return $returnResponse->errorResponse('Строка менее 3 символов');
        }
        $users = $this->db->table((new User())->getTable())
            ->where('name', 'LIKE', "{$name}%")->orWhere('surname', 'LIKE', "{$name}%")
            ->get(['id', 'name', 'surname'])->all();
        return $returnResponse->successResponse($users);
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $dbRequest = $this->db->table((new User())->getTable());
        if ($departmentId = $request->getParam('departmentId')) {
            $dbRequest = $dbRequest->where('department_id', $departmentId);
        }
        $users = $dbRequest->get(['id', 'name', 'surname'])->all();
        return $returnResponse->successResponse($users);
    }

    public function info(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $user = $this->db->table((new User())->getTable())->where('id', $id)->get()->shift();
        if (empty($user)) {
            return $returnResponse->errorResponse('Такого пользователя не существует');
        }
//        $tableMerch = (new Merch())->getTable();
//        $tablePurchases = (new Purchases())->getTable();
//        $stimulus = [];//$this->container['db']::select("SELECT p.name, s.balls, s.comment, DATE_FORMAT(s.date, '%d.%m.%Y') AS date FROM {$tableStimulus} s INNER JOIN {$tablePromo} p ON s.promo_id = p.id WHERE s.user_id = {$id} ORDER BY s.date");
//        $purchases = $this->container['db']::select("SELECT m.name, p.price FROM {$tablePurchases} p INNER JOIN {$tableMerch} m ON p.merch_id = m.id WHERE p.user_id = {$id}");
//        $totalRating = 0;
//        $totalBalance = $totalRating;
//        foreach ($purchases as $purchase) {
//            $totalBalance -= (int)$purchase->price;
//        }
        $challengeParse = $achievementParse = [];
        $challenges = $this->db->table((new Challenge())->getTable())->get()->where('responsible_id', $id)->all();
        foreach ($challenges as $challenge) {
            $challengeParse[] = $this->formatChallenge($challenge);
        }
        $achievements = $this->db->table((new Achievement())->getTable())->get()->all();
        foreach ($achievements as $achievement) {
            $achievementParse[] = [
                'name' => $achievement->name,
                'price' => 100,
                'date' => '20.11.2022',
                'challenge' => 'Челлендж',
            ];
        }
        return $returnResponse->successResponse([
            'name' => $user->name,
            'surname' => $user->surname,
            'avatar' => (new Server())->getHost() . '/images/user/' . $user->id . '.jpg',
            'balance' => 120,
            'achievements' => $achievementParse,
            'challenges' => $challengeParse,
        ]);
    }
}