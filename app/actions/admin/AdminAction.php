<?php

namespace actions\admin;

use Psr\Container\ContainerInterface;

class AdminAction
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}