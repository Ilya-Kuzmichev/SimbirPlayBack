<?php

namespace actions;

use helpers\Image;
use helpers\ReturnedResponse;
use helpers\Server;
use models\Bonus;
use models\Merch;
use models\Purchases;
use models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class MerchAction
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
        $merchList = $this->db->table((new Merch())->getTable())->get(['id', 'name', 'price', 'picture'])->sortBy('price')->all();
        foreach ($merchList as $index => $merch) {
            $merchList[$index]->picture = (new Server())->getHost() . '/images/merch/' . $merch->id . '.png';
        }
        return $returnResponse->successResponse(array_values($merchList));
    }

    public function create(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = [
            'name' => $request->getParam('name'),
            'price' => $request->getParam('price'),
        ];
        if ($pictureBase64 = $request->getParam('picture')) {
            if ($picture = (new Image())->base64ToImage($pictureBase64, $this->container->uploadDir . 'merch/')) {
                $attributes['picture'] = $picture;
            }
        }
        $merch = new Merch();
        if ($errors = $this->container->validator->validate($attributes, [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
            'price' => Validator::noWhitespace()->intVal()->between(0, 10000),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $merch->fill($attributes);
        if ($merch->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }

    public function buy(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $id = $args['id'] ?? null;
        $merch = $this->db->table((new Merch())->getTable())->where('id', $id)->get()->shift();
        if (empty($merch)) {
            return $returnResponse->errorResponse('Такого товара не существует');
        }
        $userId = $request->getParam('userId');
        if (!$this->db->table((new User())->getTable())->where('id', $userId)->get()->shift()) {
            return $returnResponse->errorResponse('Такого пользователя не существует');
        }
        $tableBonus = (new Bonus())->getTable();
        $bonus = $this->db::select("SELECT SUM(bonus) bonus FROM {$tableBonus} WHERE user_id = {$userId}");
        if ($bonus < $merch->price) {
            return $returnResponse->errorResponse('Не хватает баланса для покупки мерча');
        }
        $attributes = [
            'user_id' => $userId,
            'merch_id' => $id,
            'price' => $merch->price,
        ];
        $purchases = new Purchases();
        $purchases->fill($attributes);
        if ($purchases->save()) {
            $attributes = [
                'user_id' => $userId,
                'bonus' => -$merch->price,
            ];
            $bonus = new Bonus();
            $bonus->fill($attributes);
            $bonus->save();
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}