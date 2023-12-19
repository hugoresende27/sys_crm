<?php

namespace App\Repositories;
use App\Config\Middleware\TokenMiddleware;
use DateTime;
use PDO;
class UserRepository
{
    const TABLE_NAME = 'users';
    private PDO $pdo;
    private string $nowTime;
    public TokenMiddleware $tokenMiddleware;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->nowTime = (new DateTime('now'))->format('Y-m-d h:i:s');
        $this->tokenMiddleware = new TokenMiddleware();
    }

    public function addUser(array $data): array
    {

        $name = $data['name'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
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
        $username = $name.'-'.substr($birthDate,2,2).$lastInsertedId;
        $data['username'] = $username;
        $token = $this->tokenMiddleware->generateToken($data);
        $updateStmt = $this->pdo->prepare('UPDATE ' . self::TABLE_NAME . 
            ' SET username = :username , token = :token WHERE id = :id');
        $updateStmt->bindParam(':username', $username);
        $updateStmt->bindParam(':id', $lastInsertedId);
        $updateStmt->bindParam(':token', $token);
        $updateStmt->execute();

       
        return ['success' => true, 'message' => 'User added successfully','username' => $username];
    }

    public function loginUser(string $username, string $password): array
    {
        $stmt = $this->pdo->prepare('SELECT id, password, username, token FROM ' . 
            self::TABLE_NAME . ' WHERE username = :username AND active = true');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $verifyToken = $this->tokenMiddleware->isValidToken($user['token']);
            return ['success' => true, 'message' => 'Login successful', 'user' => $user, 'token' => $verifyToken];
        } else {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }


    public function getAllUsers(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . self::TABLE_NAME);   
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function userNameAndEmailExist(string $name, string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . self::TABLE_NAME
            .' WHERE  name = :name AND email = :email');   
    
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($r);
    }
    public function getUserById(int $id): bool | array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . self::TABLE_NAME
            .' WHERE  id = :id');   
    
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function updateUser(int $userId, array $data): void
    {
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;

        $updateFields = [];
        if ($name !== null) {
            $updateFields[] = 'name = :name';
        }
        if ($email !== null) {
            $updateFields[] = 'email = :email';
        }
        if ($phone !== null) {
            $updateFields[] = 'phone = :phone';
        }

        if (empty($updateFields)) {
            return;
        }

        $updateFields[] = 'updated_at = :updated_at';

        $sql = 'UPDATE ' . self::TABLE_NAME . ' SET ' . implode(', ', $updateFields) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $userId);
        if ($name !== null) {
            $stmt->bindParam(':name', $name);
        }
        if ($email !== null) {
            $stmt->bindParam(':email', $email);
        }
        if ($phone !== null) {
            $stmt->bindParam(':phone', $phone);
        }        
        $stmt->bindParam(':updated_at', $this->nowTime);
        $stmt->execute();
    }

    public function deleteUser(int $userId): bool
    {
        $sql = 'DELETE FROM ' . self::TABLE_NAME . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return (is_array($this->getUserById($userId) ?? false));
    }

}
