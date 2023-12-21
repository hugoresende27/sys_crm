<?php

declare(strict_types=1);

namespace App\Config;

use Psr\Http\Message\ResponseInterface as Response;

final class JsonResponse
{
    public static function withJson(
        Response $response,
        $data,
        int $status = 200
    ): Response {

        try {
            if(!is_array($data)){
                //Convert data into array
                $array = array();
                $array['response'] = $data;
                $data = $array;
            }
            $data['status'] = [
                'success' => true,
                'timestamp' => time()
            ];

            $response->getBody()->write(json_encode($data));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
        } catch (\Exception $e){
            echo $e->getMessage();
            return JsonResponse::withErrorJson($response,$e->getMessage(),500);
        }
    }

    public static function withErrorJson(
        Response $response,
        string $errorMessage,
        int $status = 400
    ): Response {


        $responseData = [
            'status' => [
                'success' => false,
                'message' => $errorMessage,
                'timestamp' => time()
            ]
        ];

        $response->getBody()->write(json_encode($responseData));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
