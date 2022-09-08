<?php

$container = $app->getContainer();
$container['settings']['displayErrorDetails'] = true;

$container['phpErrorHandler'] = function ($container) {
    return function ($request, $response, $error) use ($container) {
        return $container['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write($error);
    };
};

$container['view'] = function () {
    return new \Slim\Views\PhpRenderer('views', [], 'layout.php');
};

$app->get('/', \action\IndexAction::class . ':home');