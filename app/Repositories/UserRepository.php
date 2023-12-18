<?php

namespace App\Repositories;
use DateTime;
use PDO;
class UserRepository
{
    const TABLE_NAME = 'users';
    private PDO $pdo;
    private string $nowTime;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->nowTime = (new DateTime('now'))->format('Y-m-d h:i:s');
    }

    public function addUser(array $data): array
    {

        $name = $data['name'];
        $password = $data['password'];
        $email = $data['email'];
        $phone = $data['phone'];
        $birthDate = $data['birth_date'];
        $active = true;
        $createdAt = $this->nowTime;


        $stmt = $this->pdo->prepare('INSERT INTO ' . self::TABLE_NAME . 
        ' (name, password, email, phone, active, birth_date, created_at)
         VALUES (:name, :password, :email, :phone,:active, :birthDate, :created_at)');

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':birthDate', $birthDate);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':active', $active);
        $stmt->execute();

        $lastInsertedId = $this->pdo->lastInsertId();
        $userName = $name.'-'.substr($birthDate,2,2).$lastInsertedId;

        $updateStmt = $this->pdo->prepare('UPDATE ' . self::TABLE_NAME . ' SET username = :username WHERE id = :id');
        $updateStmt->bindParam(':username', $userName);
        $updateStmt->bindParam(':id', $lastInsertedId);
        $updateStmt->execute();
        return ['success' => true, 'message' => 'User added successfully','username' => $userName];
    }

}
