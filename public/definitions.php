<?php

use helpers\ReturnedResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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

$app->post('/user/authentication', \actions\UserAction::class . ':authentication');
$app->get('/user/list', \actions\UserAction::class . ':list');
$app->get('/user/info/{id}', \actions\UserAction::class . ':info');
$app->post('/user/create-stimulus/{id}', \actions\UserAction::class . ':createStimulus');

$app->get('/achievement/group-list', \actions\AchievementAction::class . ':groupList');
$app->get('/achievement/list', \actions\AchievementAction::class . ':list');

$app->get('/merch/list', \actions\MerchAction::class . ':list');
$app->post('/merch/create', \actions\MerchAction::class . ':create');
$app->post('/merch/buy/{id}', \actions\MerchAction::class . ':buy');

$app->get('/challenge/list', \actions\ChallengeAction::class . ':list');
$app->get('/challenge/info/{id}', \actions\ChallengeAction::class . ':info');
$app->post('/challenge/create', \actions\ChallengeAction::class . ':create');

$app->options('/user/create-stimulus/{id}', function (Request $request, Response $response, $args) {
    ReturnedResponse::responseForOptionsRequest();
});
$app->options('/merch/create', function (Request $request, Response $response, $args) {
    ReturnedResponse::responseForOptionsRequest();
});
$app->options('/merch/buy/{id}', function (Request $request, Response $response, $args) {
    ReturnedResponse::responseForOptionsRequest();
});
$app->options('/achievement/create', function (Request $request, Response $response, $args) {
    ReturnedResponse::responseForOptionsRequest();
});
$app->options('/user/challenge', function (Request $request, Response $response, $args) {
    ReturnedResponse::responseForOptionsRequest();
});

$app->post('/admin/achievement/create-group', \actions\admin\AchievementAction::class . ':createGroup');
$app->post('/admin/achievement/create', \actions\admin\AchievementAction::class . ':create');