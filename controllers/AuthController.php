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

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? ''; // Corregir typo
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $rol = $_POST['rol'] ?? '';
    
            if (empty($username) || empty($password) || empty($rol)) {
                $this->redirectWithError('Por favor, completa todos los campos.','register');
                return;
            } elseif($password !== $confirm_password) {
                $this->redirectWithError('Las contraseñas no coinciden.','register');
                return;
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->redirectWithError('El email no es válido.','register');
                return;
            }
            
            $user = $this->userModel->register($username, $password, $email, $telefono, $rol);
            if ($user) {
                $this->redirectWithSuccess('Usuario registrado correctamente.','register');
                return;
            } else {
                $this->redirectWithError('Error al registrar el usuario.','register');
                return;
            }
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
    
      // Método unificado para redirecciones
      private function redirectWithError($message, $page = 'login') {
        $_SESSION['error_message'] = $message;
        
        switch ($page) {
            case 'register':
                header('Location: ../register.php');
                break;
            case 'login':
            default:
                header('Location: ../index.php');
                break;
        }
        exit;
    }
    
    private function redirectWithSuccess($message, $page = 'login') {
        $_SESSION['success_message'] = $message;
        
        switch ($page) {
            case 'register':
                header('Location: ../register.php');
                break;
            case 'login':
            default:
                header('Location: ../index.php');
                break;
        }
        exit;
    }
}

?>