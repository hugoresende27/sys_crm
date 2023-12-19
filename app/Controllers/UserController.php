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

        //verify if name or email exist
        if ($this->userRepository->userNameAndEmailExist($v->data()['name'], $v->data()['email'])){
            $response->getBody()->write('ERROR :: user with name '.$v->data()['name'].' and email '.$v->data()['email'].'exists');
            return $response;
        }
        
        if ($v->validate()) {
            $addUser = $this->userRepository->addUser($v->data());
            $response->getBody()->write(json_encode($addUser));
        } else {
            $response->getBody()->write(json_encode($v->errors()));
        }


        return $response;
    }

    public function loginUser(Request $request, Response $response): Response
    {
        $body = $request->getBody()->getContents();
        $userPostData = json_decode($body, true);

        $v = new Validator($userPostData);
        $v->rule('required', ['username', 'password']);

        if ($v->validate()){
            $loginUser = $this->userRepository->loginUser($v->data()['username'], $v->data()['password']);
            if(isset($loginUser['token']) && $loginUser['token'] == true){
                $response->getBody()->write('Login with sucess -- TOKEN::'.json_encode($loginUser['user']['token']));
            } else {
                $response->getBody()->write('Wrong credentials');
            }
         
        } else {
            $response->getBody()->write(json_encode($v->errors()));
        }

        return $response;

    }

    /**
     * [ getUsers all fileds of all users]
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getUsers(Request $request, Response $response): Response
    {
        $data = $this->userRepository->getAllUsers();
        $response->getBody()->write(json_encode($data));
        return $response;
    }


    /**
     * [editUser]
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function editUser(Request $request, Response $response, array $args): Response
    {
        $userId = $args['id'];
        $user = $this->userRepository->getUserById($userId);

        if (!$user) {
            $response->getBody()->write('User not found');
            return $response->withStatus(404);
        }

        $body = $request->getBody()->getContents();
        $userData = json_decode($body, true);
        $v = new Validator($userData);
        $v->rule('required', ['name', 'password', 'email', 'phone', 'birth_date']);
        $v->rule('email', 'email');
        $v->rule('alpha', 'name'); 
        $v->rule('min', 'password', 6); 
        $v->rule('dateFormat', 'birth_date', 'Y-m-d');

        $this->userRepository->updateUser($userId, $userData);

        $response->getBody()->write('User updated successfully');
        return $response;
    }


    /**
     * [deleteUser]
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $userId = $args['id'];
        $user = $this->userRepository->getUserById($userId);

        if (!$user) {
            $response->getBody()->write('User not found');
            return $response->withStatus(404);
        }

        if (!($this->userRepository->deleteUser($userId))){
            $response->getBody()->write('User deleted successfully');
        } else {
            $response->getBody()->write('ERROR :: deleting user error');
        }
        return $response;
    }

    

}
