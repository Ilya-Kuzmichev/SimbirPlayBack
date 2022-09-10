<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Promo;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class PromoAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $merch = $this->container['db']->table((new Promo())->getTable())->get(['id', 'name', 'default_rating as defaultRating'])->all();
        return $returnResponse->successResponse($merch);
    }

    public function create(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = [
            'name' => $request->getParam('name'),
            'default_rating' => $request->getParam('defaultRating'),
        ];
        $promo = new Promo();
        if ($errors = $this->container->validator->validate($attributes, [
            'name' => Validator::noWhitespace()->notEmpty()->stringType()->length(1, 255),
            'default_rating' => Validator::noWhitespace()->intVal()->between(0, 10000),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $promo->fill($attributes);
        if ($promo->save()) {
            return $returnResponse->successResponse();
        }
        return $returnResponse->saveErrorResponse();
    }
}