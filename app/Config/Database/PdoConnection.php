<?php

namespace App\Config\Database;

use PDO;
use PDOException;

class PdoConnection
{
    private static ?PDO $pdoInstance = null;

    private function __construct()
    {
        // Private constructor to prevent instantiation
    }

    public static function getInstance(
        string $host,
        string $dbName,
        string $port,
        string $user,
        string $pass
    ): PDO
    {
        if (self::$pdoInstance === null) {
            try {
                $dsn = 'mysql:host=' .$host . ';dbname=' . $dbName . ';port=' . $port . ';charset=utf8mb4';
                self::$pdoInstance = new PDO( $dsn , $user , $pass );
                self::$pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdoInstance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }

        return self::$pdoInstance;
    }
}
