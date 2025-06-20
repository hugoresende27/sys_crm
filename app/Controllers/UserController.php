<?php

namespace App\Controllers;

use App\Config\JsonResponse;
use App\Repositories\UserRepository;
use DateTime;
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
            $response->getBody()->write('ERROR :: user with name '.$v->data()['name'].' and email '.$v->data()['email'].' exists');
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
            $time = (new DateTime('now'));
            $loginUser = $this->userRepository->loginUser($v->data()['username'], $v->data()['password']);
            if(isset($loginUser['token']) && $loginUser['token'] == true){
                $data['user_id'] =  $loginUser['user']['id'];
                $data['created_at'] =  $time->format('Y-m-d H:i:s');
                $data['expires_at'] =  $time->modify('+1 hour')->format('Y-m-d H:i:s');
                $data['token'] = $loginUser['user']['token'];
                return JsonResponse::withJson($response, $data);
            } else {
                $data = 'Wrong credentials';
                return JsonResponse::withJson($response, $data, 401);
            }
         
        } else {
            $data = json_encode($v->errors());
            return JsonResponse::withErrorJson($response, $data);
        }
  
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

        $updatedFields = $this->userRepository->updateUser($userId, $userData);

        $response->getBody()->write('User updated successfully');
        return JsonResponse::withJson($response, $updatedFields);
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
