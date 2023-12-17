<?php
namespace App\Middleware;
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

        // Validate the token (you may want to use a library or implement your validation logic)

        if ($this->isValidToken($token)) {
        
            // Token is valid, proceed to the next middleware or route handler
            return $handler->handle($request);
        } else {
            // Token is not valid, return unauthorized response
            print_r('unauthorized');
            return new Response(401);
        }
    }

    private function isValidToken(string $token): bool
    {
        try {
            // JWT::decode($token, 'your-secret-key', ['HS256']);
            $this->handleJWT();
            return true;
        } catch (\Exception $e) {
            // Token is invalid
            return false;
        }
    }

    private function handleJWT()
    {
        $key = 'example_key';
        $payload = [
            'iss' => 'http://example.org',
            'aud' => 'http://example.com',
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
        print_r($decoded);
   
 
        // Pass a stdClass in as the third parameter to get the decoded header values
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'), $headers = new stdClass());
        print_r($headers);

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
        JWT::$leeway = 60; // $leeway in seconds
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        dd($decoded);
        return $decoded;
    }
    
}
