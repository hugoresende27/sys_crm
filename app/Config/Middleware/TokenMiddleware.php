<?php
namespace App\Config\Middleware;
use DateTimeImmutable;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;
class TokenMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');
        
        if (empty($token)) {
            $response = new Response(401);
            $response->getBody()->write('Unauthorized');
            return $response;
        }
        $isValidToken = $this->isValidToken($token);
        if(isset($isValidToken['status'])) {
            if ($isValidToken['status']) {
                return $handler->handle($request);
            }
        }
        $response = new Response(400);
        $response->getBody()->write(json_encode($isValidToken));
        return $response;


    }

    public function isValidToken(string $token): array
    {
       
        try {
            $decodedToken = JWT::decode($token, new Key($_ENV['APP_KEY'], 'HS256'));
      
            if (!isset($decodedToken->expiration_date)) {
                return ['status' => false, 'error' => 'token invalid'];
            }
            $expirationDate = new DateTimeImmutable($decodedToken->expiration_date->date);
            $currentDateTime = new DateTimeImmutable();
            // dd($currentDateTime , $expirationDate);
            return ($currentDateTime > $expirationDate)
                ? ['status' => false, 'error' => 'token expired']
                : ['status' => true];
        } catch (\Exception $e) {
            return ['status' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function generateToken(array $data): string
    {
        $payload = [
            'iss' => $_ENV['APP_HOST'],
            'aud' => $_ENV['APP_NAME'],
            'user_data' => $data,
            'expiration_date' => (new DateTimeImmutable())->modify('+1 hour')
        ];
        return JWT::encode($payload, $_ENV['APP_KEY'], 'HS256');
    }


    private function handleJWT()
    {
        $key = 'example_key';
        $payload = [
            'iss' => $_ENV['APP_HOST'],
            'aud' => $_ENV['APP_HOST'],
            'iat' => 1356999524,
            'nbf' => 1357000000
        ];

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload, $key, 'HS256');

  

        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        // print_r($decoded);
   
 
        // Pass a stdClass in as the third parameter to get the decoded header values
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'), $headers = new stdClass());
        // print_r($headers);

        /*
        NOTE: This will now be an object instead of an associative array. To get
        an associative array, you will need to cast it as such:
        */

        $decoded_array = (array) $decoded;

        /**
         * You can add a leeway to account for when there is a clock skew times between
         * the signing and verifying servers. It is recommended that this leeway should
         * not be bigger than a few minutes.
         *
         * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
         */
        JWT::$leeway = 600; // $leeway in seconds
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // dd($jwt, $decoded_array);
        return $decoded;
    }
    
}
