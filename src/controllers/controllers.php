<?php

namespace controllers;
use Slim\Router;

class controllers {

    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
}