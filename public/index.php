<?php
session_start();

// Obtener parámetros de la URL
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

// Incluir archivos necesarios
require_once '../config/database.php';

try {
    // Cargar el controlador correspondiente
    switch ($controller) {
        case 'auth':
            require_once '../controllers/AuthController.php';
            $authController = new AuthController($pdo);
            
            switch ($action) {
                case 'login':
                    $authController->login();
                    break;
                case 'logout':
                    $authController->logout();
                    break;
                default:
                    header('Location: ../index.php');
                    exit;
            }
            break;
            
        case 'dashboard':
            // Verificar si el usuario está logueado
            if (!isset($_SESSION['user_id'])) {
                header('Location: ../index.php');
                exit;
            }
            require_once '../views/dashboard/index.php';
            break;
            
        case 'profile':
            // Verificar si el usuario está logueado
            if (!isset($_SESSION['user_id'])) {
                header('Location: ../index.php');
                exit;
            }
            require_once '../controllers/profileController.php';
            $profileController = new ProfileController($pdo);
            
            switch ($action) {
                case 'show':
                    require_once '../views/profile/index.php';
                    break;
                case 'update':
                    $result = $profileController->updateProfile($_POST);
                    if ($result['success']) {
                        $_SESSION['profile_message'] = $result['message'];
                        $_SESSION['profile_message_type'] = 'success';
                    } else {
                        $_SESSION['profile_message'] = $result['error'] ?? 'Error al actualizar el perfil';
                        $_SESSION['profile_message_type'] = 'error';
                    }
                    header('Location: index.php?controller=profile&action=show');
                    exit;
                case 'change_password':
                    $result = $profileController->changePassword($_POST['current_password'], $_POST['new_password']);
                    if ($result['success']) {
                        $_SESSION['profile_message'] = $result['message'];
                        $_SESSION['profile_message_type'] = 'success';
                    } else {
                        $_SESSION['profile_message'] = $result['error'];
                        $_SESSION['profile_message_type'] = 'error';
                    }
                    header('Location: index.php?controller=profile&action=show');
                    exit;
                case 'upload_picture':
                    $result = $profileController->uploadPicture($_FILES['avatar']);
                    if ($result['success']) {
                        $_SESSION['profile_message'] = 'Foto de perfil actualizada exitosamente';
                        $_SESSION['profile_message_type'] = 'success';
                    } else {
                        $_SESSION['profile_message'] = $result['error'];
                        $_SESSION['profile_message_type'] = 'error';
                    }
                    header('Location: index.php?controller=profile&action=show');
                    exit;
                default:
                    require_once '../views/profile/index.php';
                    break;
            }
            break;
            
        case 'libro':
            // Verificar si el usuario está logueado
            if (!isset($_SESSION['user_id'])) {
                header('Location: ../index.php');
                exit;
            }
            require_once '../controllers/LibroController.php';
            $libroController = new LibroController();
            
            switch ($action) {
                case 'index':
                    $libroController->index();
                    break;
                case 'create':
                    $libroController->create();
                    break;
                case 'store':
                    $libroController->store();
                    break;
                case 'show':
                    $libroController->show($_GET['id'] ?? null);
                    break;
                case 'edit':
                    $libroController->edit($_GET['id'] ?? null);
                    break;
                case 'update':
                    $libroController->update($_GET['id'] ?? null);
                    break;
                case 'delete':
                    $libroController->delete($_GET['id'] ?? null);
                    break;
                case 'asignarFirmantes':
                    $libroController->assignSignatories();
                    break;
                case 'updateEstadoFirma':
                    $libroController->updateSignatureStatus();
                    break;
                default:
                    $libroController->index();
                    break;
            }
            break;
            
        default:
            header('Location: ../index.php');
            exit;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>