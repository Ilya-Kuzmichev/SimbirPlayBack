<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Merch;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class MerchAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $merch = $this->container['db']->table((new Merch())->getTable())->get(['id', 'name', 'price'])->all();
        return $returnResponse->successResponse($merch);
    }

    public function create(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = [
            'name' => $request->getParam('name'),
            'price' => $request->getParam('price'),
        ];
        $merch = new Merch();
        if ($errors = $this->container->validator->validate($attributes, [
            'name' => Validator::noWhitespace()->notEmpty()->stringType()->length(1, 255),
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
}