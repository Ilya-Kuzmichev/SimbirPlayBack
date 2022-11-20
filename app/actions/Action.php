<?php

namespace actions;

use Psr\Container\ContainerInterface;

class Action
{
    protected $container;
    protected $validator;
    protected $db;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->db = $container->db;
        $this->validator = $container->validator;
    }
}