<?php

namespace App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use Valitron\Validator;
class SystemController
{

    private ContainerInterface $container;
    private $pdo;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pdo = $this->container->get('pdo');
    }

    /**
     * [createSQLTable]
     * post request to create a table in SQL database
     * Example
     *   {
     *      "table_name" : "rabbitmq_controll",
     *       "columns": 
     *         {
     *            "item_code" : "VARCHAR",
     *            "updated_at" : "DATETIME"
     *         }
     *   }
     */
    public function addSQLTableIfNotExist(Request $request, Response $response): Response
    {
       
        $postData = $request->getParsedBody();
        $body = $request->getBody()->getContents();
        $postData = json_decode($body, true);

        $tableName = $postData['table_name'];
     
        $sql = "SHOW TABLES";
        $stmt = $this->pdo->query($sql);
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
   
        if (in_array( $tableName , $tables)){
            $response->getBody()->write('Table already exist');
            return $response;
        }

        $createTableSQL = "CREATE TABLE $tableName (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        foreach($postData['columns'] as $columnName => $columnType){
            if ($columnType == strtoupper('varchar')){
                $columnType = 'VARCHAR (255)';
            }
            if (!end($postData['columns'])){
                $createTableSQL .= "$columnName $columnType";
            } else {
                $createTableSQL .= ", $columnName $columnType ";
            }
    
        }

        $createTableSQL = rtrim($createTableSQL, ' ') . ")";
        
        $stmt = $this->pdo->prepare($createTableSQL);
        $res = $stmt->execute();
        $response->getBody()->write(json_encode($res));

        return $response;

    }

    public function dev(Request $request, Response $response, $args) 
    {

        // throw new InvalidArgumentException('Invalid date format. Please use YYYY-MM-DD.');

        dd($_ENV['APP_LOCAL']);
        if (extension_loaded('sodium')) {
            echo 'Libsodium is installed.';
        } else {
            echo 'Libsodium is not installed.';
        };
        die();
        $response->getBody()->write(json_encode($r ?? ""));
        return $response;
    }
}