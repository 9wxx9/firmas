<?php
require_once '../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);
            
            if (empty($username) || empty($password)) {
                $this->redirectWithError('Por favor, completa todos los campos.');
                return;
            }
            
            $user = $this->userModel->authenticate($username, $password);
            
            if ($user) {
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['rol'] = $user['rol'];
                
                // Manejar "Recordarme"
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->updateRememberToken($user['id'], $token);
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true); // 30 días
                }
                
                // Redirigir al dashboard
                header('Location: index.php?controller=dashboard');
                exit;
            } else {
                $this->redirectWithError('Usuario o contraseña incorrectos.');
            }
        } else {
            // Si no es POST, redirigir al login
            header('Location: ../index.php');
            exit;
        }
    }
    
    public function logout() {
        // Limpiar sesión
        session_destroy();
        
        // Limpiar cookie de recordar
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        // Redirigir al login
        header('Location: ../index.php');
        exit;
    }
    
    private function redirectWithError($message) {
        $_SESSION['error_message'] = $message;
        header('Location: ../index.php');
        exit;
    }
}
?>