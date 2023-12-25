<?php

namespace App\Controllers;

use App\Config\JsonResponse;
use App\Repositories\UserRepository;
use App\webServices\TheMovieDbAPI;
use DateTime;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Valitron\Validator;

class MovieController
{
    private ContainerInterface $container;
    private TheMovieDbAPI $movieAPI;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->movieAPI = new TheMovieDbAPI();

    }

    public function getTrendings(Request $request, Response $response): Response
    {
        $data = $this->movieAPI->trendings();
        return JsonResponse::withJson($response, $data);
    }
    public function getPopularity(Request $request, Response $response): Response
    {
        $data = $this->movieAPI->popularity();
        return JsonResponse::withJson($response, $data);
    }
    public function getByGenre(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $data = $this->movieAPI->byGenre($queryParams['genre_id'] ?? null);
        return JsonResponse::withJson($response, $data);
    }

    

}
