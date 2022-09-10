<?php

namespace actions;

use helpers\Image;
use helpers\ReturnedResponse;
use helpers\Server;
use models\Merch;
use models\Purchases;
use models\Stimulus;
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
        $merchList = $this->db->table((new Merch())->getTable())->get(['id', 'name', 'price', 'picture'])->all();
        foreach ($merchList as $index => $merch) {
            $merchList[$index]->picture = $merch->picture ? (new Server())->getHost() . '/images/merch/' . $merch->picture : '';
        }
        return $returnResponse->successResponse($merchList);
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
        $tableStimulus = (new Stimulus())->getTable();
        $tablePurchases = (new Purchases())->getTable();
        $stimulusBalance = $this->db::select("SELECT SUM(balls) AS balls FROM {$tableStimulus} WHERE user_id = {$userId}");
        $stimulusBalance = $stimulusBalance ? array_shift($stimulusBalance) : [];
        $stimulusBalance = !empty($stimulusBalance->balls) ? (int)$stimulusBalance->balls : 0;

        $purchasesBalance = $this->db::select("SELECT SUM(price) AS price FROM {$tablePurchases} WHERE user_id = {$userId}");
        $purchasesBalance = $purchasesBalance ? array_shift($purchasesBalance) : [];
        $purchasesBalance = !empty($purchasesBalance->price) ? (int)$purchasesBalance->price : 0;
        $totalBalance = $stimulusBalance - $purchasesBalance;
        if ($totalBalance < $merch->price) {
            return $returnResponse->errorResponse('Не хватает баланса для покупки мерча');
        }
        $attributes = [
            'user_id' => $userId,
            'merch_id' => $id,
            'price' => $merch->price,
            'address' => $request->getParam('address'),
        ];
        $purchases = new Purchases();
        if ($errors = $this->container->validator->validate($attributes, [
            'address' => Validator::stringType()->notEmpty()->length(1, 255),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $purchases->fill($attributes);
        if ($purchases->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}