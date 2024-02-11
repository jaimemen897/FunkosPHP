<?php

namespace services;

use Exception;
use models\User;
use PDO;

require_once __DIR__ . '/../models/User.php';

class UsersService
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @throws Exception
     */
    public function authenticate($username, $password): User
    {
        $user = $this->findUserByUsername($username);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        throw new Exception('Usuario o contraseña incorrectos');
    }

    public function save(User $user)
    {
        $sql = "SELECT NEXTVAL('users_id_seq')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $userId = $stmt->fetchColumn();

        $hashPassword = password_hash($user->password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (id, username, password, name, surnames, email, created_at, updated_at, is_deleted)
        VALUES (:id, :username, :password, :name, :surnames, :email, NOW(), NOW(), false)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':username', $user->username);
        $stmt->bindValue(':password', $hashPassword);
        $stmt->bindValue(':name', $user->name);
        $stmt->bindValue(':surnames', $user->surnames);
        $stmt->bindValue(':email', $user->email);

        $stmt->execute();

        foreach ($user->roles as $role) {
            $sql = "INSERT INTO user_roles (user_id, roles) VALUES (:user_id, :roles)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':roles', $role);
            $stmt->execute();
        }

        return true;
    }

    public function findUserByUsername($username): User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userRow) {
            throw new Exception('Usuario no encontrado');
        }

        $stmtRoles = $this->db->prepare("SELECT roles FROM user_roles WHERE user_id = :user_id");
        $stmtRoles->bindParam(':user_id', $userRow['id']);
        $stmtRoles->execute();
        $roles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);

        return new User(
            $userRow['id'],
            $userRow['username'],
            $userRow['password'],
            $userRow['name'],
            $userRow['surnames'],
            $userRow['email'],
            $userRow['created_at'],
            $userRow['updated_at'],
            $userRow['is_deleted'],
            $roles
        );
    }
}