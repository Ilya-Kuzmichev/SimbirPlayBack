<?php

namespace action;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class IndexAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function home(Request $request, Response $response, $args)
    {
        $this->container->view->render($response, 'home.php');
        return $response;
    }
}