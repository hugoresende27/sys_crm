<?php

namespace App\Repositories;
use PDO;
class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addUser()
    {
        // Example query
        $stmt = $this->pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        $data = $stmt->fetchAll();
        dd($data);
    }
}
