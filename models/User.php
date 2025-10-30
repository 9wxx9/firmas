<?php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function authenticate($username, $password)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, password, nombre, email, rol FROM usuarios WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }

            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateRememberToken($userId, $token)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET remember_token = ? WHERE id = ?");
            return $stmt->execute([$token, $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function register($username, $password, $email, $telefono, $rol)
    {
        try {
            // Hash de la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("INSERT INTO usuarios (username, password, email, telefono, rol) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$username, $hashedPassword, $email, $telefono, $rol]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserByRememberToken($token)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, nombre, email, rol FROM usuarios WHERE remember_token = ?");
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUser($userId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateUser($userId, $data)
    {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE usuarios SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?"
            );
            return $stmt->execute([
                $data['nombre'],
                $data['email'],
                $data['telefono'] ?? null,
                $data['direccion'] ?? null,
                $userId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function changePassword($userId, $newPassword)
    {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function uploadpicture($userId, $imagePath)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id = ?");
            return $stmt->execute([$imagePath, $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, nombre, email, rol FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
