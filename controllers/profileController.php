<?php
require_once '../models/User.php';

class ProfileController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    public function showProfile() {
        try {
            $user = $this->userModel->getUser($_SESSION['user_id']);
            if (!$user) {
                throw new Exception('Usuario no encontrado');
            }
            return $user;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function updateProfile($data) {
        try {
            $errors = $this->validateProfileData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }
            
            $result = $this->userModel->updateUser($_SESSION['user_id'], $data);
            return ['success' => $result, 'message' => $result ? 'Perfil actualizado' : 'Error al actualizar'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function changePassword($currentPassword, $newPassword) {
        try {
            // Verificar contraseña actual
            $user = $this->userModel->getUser($_SESSION['user_id']);
            if (!password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'error' => 'Contraseña actual incorrecta'];
            }
            
            // Validar fortaleza de la nueva contraseña
            $passwordValidation = $this->validatePassword($newPassword);
            if (!$passwordValidation['valid']) {
                return ['success' => false, 'error' => $passwordValidation['message']];
            }
            
            $result = $this->userModel->changePassword($_SESSION['user_id'], $newPassword);
            return ['success' => $result, 'message' => $result ? 'Contraseña cambiada exitosamente' : 'Error al cambiar contraseña'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function uploadPicture($file) {
        try {
            // Validar archivo
            if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'error' => 'Error en la subida del archivo'];
            }
            
            // Validar tipo de archivo
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                return ['success' => false, 'error' => 'Tipo de archivo no permitido'];
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $uploadPath = '../uploads/avatars/' . $filename;
            
            // Crear directorio si no existe
            if (!is_dir('../uploads/avatars/')) {
                mkdir('../uploads/avatars/', 0755, true);
            }
            
            // Mover archivo
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $result = $this->userModel->uploadpicture($_SESSION['user_id'], $filename);
                return ['success' => $result, 'filename' => $filename];
            }
            
            return ['success' => false, 'error' => 'Error al guardar el archivo'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function validateProfileData($data) {
        $errors = [];
        
        // Validar nombre
        if (empty(trim($data['nombre'] ?? ''))) {
            $errors['nombre'] = 'El nombre es requerido';
        } elseif (strlen($data['nombre']) < 2) {
            $errors['nombre'] = 'El nombre debe tener al menos 2 caracteres';
        }
        
        // Validar email
        if (empty(trim($data['email'] ?? ''))) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        } elseif (!$this->validateEmailUniqueness($data['email'], $_SESSION['user_id'])) {
            $errors['email'] = 'Este email ya está en uso por otro usuario';
        }
        
        // Validar teléfono (opcional)
        if (!empty($data['telefono']) && !preg_match('/^[0-9+\-\s()]+$/', $data['telefono'])) {
            $errors['telefono'] = 'El teléfono no es válido';
        }
        
        return $errors;
    }

    public function validatePassword($password) {
        $errors = [];
        
        // Longitud mínima
        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        // Al menos una letra mayúscula
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra mayúscula';
        }
        
        // Al menos una letra minúscula
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una letra minúscula';
        }
        
        // Al menos un número
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un número';
        }
        
        // Al menos un carácter especial
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un carácter especial (!@#$%^&*(),.?":{}|<>)';
        }
        
        if (empty($errors)) {
            return ['valid' => true, 'message' => 'Contraseña válida'];
        } else {
            return ['valid' => false, 'message' => implode('. ', $errors)];
        }
    }

    private function validateEmailUniqueness($email, $userId = null) {
        try {
            $existingUser = $this->userModel->getUserByEmail($email);
            if ($existingUser && (!$userId || $existingUser['id'] != $userId)) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}