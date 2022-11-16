<?php

namespace actions;

use helpers\ReturnedResponse;
use models\Achievement;
use models\AchievementGroup;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AchievementAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function groupList(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $groups = $this->container['db']->table((new AchievementGroup())->getTable())->get(['id', 'name'])->all();
        return $returnResponse->successResponse($groups);
    }

    public function list(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $achievements = $this->container['db']->table((new Achievement())->getTable())->get(['id', 'name'])->all();
        return $returnResponse->successResponse($achievements);
    }
}