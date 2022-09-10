<?php

$container = $app->getContainer();
$container['config'] = require_once 'config.php';
$container['settings']['displayErrorDetails'] = true;
$container['uploadDir'] = __DIR__ . '/images/';

$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['config']['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};
$container['db']::connection();

$container['validator'] = function ($container) {
    return new \helpers\Validator();
};

$container['phpErrorHandler'] = function ($container) {
    return function ($request, $response, $error) use ($container) {
        return $container['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write($error);
    };
};

$app->get('/rating', \actions\RatingAction::class . ':list');

$app->get('/departament/list', \actions\DepartamentAction::class . ':list');

$app->get('/user/list', \actions\UserAction::class . ':list');
$app->get('/user/info/{id}', \actions\UserAction::class . ':info');
$app->post('/user/create-stimulus/{id}', \actions\UserAction::class . ':createStimulus');

$app->get('/promo/list', \actions\PromoAction::class . ':list');
$app->post('/promo/create', \actions\PromoAction::class . ':create');

$app->get('/merch/list', \actions\MerchAction::class . ':list');
$app->post('/merch/create', \actions\MerchAction::class . ':create');
$app->post('/merch/buy/{id}', \actions\MerchAction::class . ':buy');