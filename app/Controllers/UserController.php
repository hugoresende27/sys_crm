<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Valitron\Validator;

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
    
        $body = $request->getBody()->getContents();
        $userPostData = json_decode($body, true);

        $v = new Validator($userPostData);
        $v->rule('required', ['name', 'password', 'email', 'phone', 'birth_date']);
        $v->rule('email', 'email');
        $v->rule('alpha', 'name'); 
        $v->rule('min', 'password', 6); 
        $v->rule('dateFormat', 'birth_date', 'Y-m-d');
        
        if ($v->validate()) {
            $addUser = $this->userRepository->addUser($v->data());
            $response->getBody()->write(json_encode($addUser));
        } else {
            $response->getBody()->write(json_encode($v->errors()));
        }


        return $response;
    }

}
