<?php

namespace actions;

use helpers\ReturnedResponse;
use helpers\Server;
use models\Merch;
use models\Promo;
use models\Purchases;
use models\Stimulus;
use Respect\Validation\Validator;
use models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserAction
{
    private $container;
    private $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container['db'];
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $dbRequest = $this->db->table((new User())->getTable());
        if ($departamentId = $request->getParam('departamentId')) {
            $dbRequest = $dbRequest->where('departament_id', $departamentId);
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
        $tableMerch = (new Merch())->getTable();
        $tablePromo = (new Promo())->getTable();
        $tableStimulus = (new Stimulus())->getTable();
        $tablePurchases = (new Purchases())->getTable();
        $stimulus = $this->container['db']::select("SELECT p.name, s.balls, s.comment, DATE_FORMAT(s.date, '%d.%m.%Y') AS date FROM {$tableStimulus} s INNER JOIN {$tablePromo} p ON s.promo_id = p.id WHERE s.user_id = {$id} ORDER BY s.date");
        $purchases = $this->container['db']::select("SELECT m.name, p.price FROM {$tablePurchases} p INNER JOIN {$tableMerch} m ON p.merch_id = m.id WHERE p.user_id = {$id}");
        $totalRating = 0;
        foreach ($stimulus as $stimulusRow) {
            $totalRating += (int)$stimulusRow->balls;
        }
        $totalBalance = $totalRating;
        foreach ($purchases as $purchase) {
            $totalBalance -= (int)$purchase->price;
        }
        return $returnResponse->successResponse([
            'name' => $user->name,
            'surname' => $user->surname,
            'avatar' => (new Server())->getHost() . '/images/user/' . $user->id . '.jpg',
            'stimulus' => $stimulus,
            'purchases' => $purchases,
            'totalRating' => $totalRating,
            'totalBalance' => $totalBalance,
        ]);
    }

    public function createStimulus(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $giverId = $request->getParam('giverId');
        $promoId = $request->getParam('promoId');
        if (!$this->db->table((new User())->getTable())->where('id', $id)->get()->shift()) {
            return $returnResponse->errorResponse('Такого пользователя не существует');
        }
        if (!$this->db->table((new User())->getTable())->where('id', $giverId)->get()->shift()) {
            return $returnResponse->errorResponse('Такого пользователя не существует');
        }
        if (!$this->db->table((new Promo())->getTable())->where('id', $promoId)->get()->shift()) {
            return $returnResponse->errorResponse('Такого поощрения не существует');
        }
        $attributes = [
            'user_id' => $id,
            'promo_id' => $promoId,
            'giver_id' => $promoId,
            'balls' => $request->getParam('balls'),
            'comment' => $request->getParam('comment') ?? '',
        ];
        $stimulus = new Stimulus();
        if ($errors = $this->container->validator->validate($attributes, [
            'balls' => Validator::noWhitespace()->intVal()->between(1, 10000),
            'comment' => Validator::stringType()->length(null, 255),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $stimulus->fill($attributes);
        if ($stimulus->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}