<?php
namespace Controllers;

use Slim\Router;

class Controllers
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
}