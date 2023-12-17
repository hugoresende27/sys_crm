<?php
// src/Controller/ExampleController.php

namespace App\Controllers;

use App\Repositories\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private ContainerInterface $container;
    private UserRepository $userRepository;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->userRepository = new UserRepository($this->container->get('pdo')); 
    }
    public function registerUser(Request $request, Response $response): Response
    {
       
        $userPostData = [];

        $addUser = $this->userRepository->addUser();
        dd($addUser);

        $response->getBody()->write('register User');

        return $response;
    }

}
