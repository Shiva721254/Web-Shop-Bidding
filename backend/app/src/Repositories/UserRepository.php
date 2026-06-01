<?php

namespace App\Repositories;

use App\Models\User;
use App\Framework\Database;
use App\Repositories\Interfaces\IUserRepository;
use PDO;

class UserRepository implements IUserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    public function create(User $user): User
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (name, email, password, role)
            VALUES (:name, :email, :password, :role)
        ');
        $stmt->execute([
            ':name'     => $user->name,
            ':email'    => $user->email,
            ':password' => $user->password,
            ':role'     => $user->role,
        ]);
        $user->id = (int)$this->db->lastInsertId();
        return $user;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
