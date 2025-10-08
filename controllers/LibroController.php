<?php
require_once __DIR__ . '/../models/Libro.php';
require_once __DIR__ . '/../models/Firma.php';
require_once __DIR__ . '/../models/Firmante.php';
require_once __DIR__ . '/../config/database.php';

class LibroController {
    private $libroModel;
    private $firmaModel;
    private $firmanteModel;
    private $pdo;
    
    public function __construct() {
        $this->pdo = $this->getConnection();
        $this->libroModel = new Libro($this->pdo);
        $this->firmaModel = new Firma($this->pdo);
        $this->firmanteModel = new Firmante($this->pdo);
    }
    
    private function getConnection() {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = require __DIR__ . '/../config/database.php';
        }
        return $pdo;
    }
    
    // Mostrar lista de libros
    public function index() {
        $libros = $this->libroModel->getAllWithFirmantes();
        require_once __DIR__ . '/../views/libros/index.php';
    }
    
    // Mostrar formulario para crear nuevo libro
    public function create() {
        
        require_once __DIR__ . '/../views/libros/create.php';
    }
    
    // Guardar nuevo libro
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'numero_referencia' => $_POST['numero_referencia'] ?? '',
                'titulo' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'mes' => $_POST['mes'] ?? '',
                'anio' => $_POST['anio'] ?? date('Y')
            ];
            
            // Validaciones
            if (!empty($_POST['firmantes'])) {
                $firmantes = $_POST['firmantes'];
                $this->libroModel->asignarFirmantes($libroId, $firmantes);
            }
            $errors = [];
            if (empty($data['numero_referencia'])) {
                $errors[] = 'El número de referencia es obligatorio';
            }
            if (empty($data['titulo'])) {
                $errors[] = 'El nombre del libro es obligatorio';
            }
            if (empty($data['mes'])) {
                $errors[] = 'El mes es obligatorio';
            }
            
            // Verificar si el número de referencia ya existe
            if ($this->libroModel->existeNumeroReferencia($data['numero_referencia'])) {
                $errors[] = 'El número de referencia ya existe';
            }
            
            if (empty($errors)) {
                // Ajustar datos para el modelo
            $modelData = [
                'numero_referencia' => $data['numero_referencia'],
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion'],
                'mes' => $data['mes'],
                'año' => $data['anio']
            ];
                
                $libroId = $this->libroModel->createLibro($modelData);
                if ($libroId) {
                    $_SESSION['message'] = 'Libro creado exitosamente';
                    $_SESSION['message_type'] = 'success';
                    header('Location: index.php?controller=libro&action=index');
                    exit;
                } else {
                    $errors[] = 'Error al crear el libro';
                }
            }
            
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header('Location: index.php?controller=libro&action=create');
            exit;
        }
    }
    
    // Mostrar detalles de un libro
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=libro&action=index');
            exit;
        }
        
        $libro = $this->libroModel->getLibroById($id);
        if (!$libro) {
            $_SESSION['message'] = 'Libro no encontrado';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=libro&action=index');
            exit;
        }
        
        $firmas = $this->firmaModel->getFirmasByLibro($id);
        $firmantes = $this->firmanteModel->getAllFirmantes();
        
        require_once __DIR__ . '/../views/libros/show.php';
    }
    
    // Mostrar formulario de edición
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=libro&action=index');
            exit;
        }
        
        $libro = $this->libroModel->getLibroById($id);
        if (!$libro) {
            $_SESSION['message'] = 'Libro no encontrado';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=libro&action=index');
            exit;
        }
        
        require_once __DIR__ . '/../views/libros/edit.php';
    }
    
    // Actualizar libro
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                header('Location: index.php?controller=libro&action=index');
                exit;
            }
            
            $data = [
                'numero_referencia' => $_POST['numero_referencia'] ?? '',
                'titulo' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'mes' => $_POST['mes'] ?? '',
                'anio' => $_POST['anio'] ?? date('Y')
            ];
            
            // Validaciones
            $errors = [];
            if (empty($data['numero_referencia'])) {
                $errors[] = 'El número de referencia es obligatorio';
            }
            if (empty($data['titulo'])) {
                $errors[] = 'El nombre del libro es obligatorio';
            }
            if (empty($data['mes'])) {
                $errors[] = 'El mes es obligatorio';
            }
            
            // Verificar si el número de referencia ya existe (excluyendo el libro actual)
            if ($this->libroModel->existeNumeroReferencia($data['numero_referencia'], $id)) {
                $errors[] = 'El número de referencia ya existe';
            }
            
            if (empty($errors)) {
                // Ajustar datos para el modelo
                $modelData = [
                    'numero_referencia' => $data['numero_referencia'],
                    'titulo' => $data['titulo'],
                    'descripcion' => $data['descripcion'],
                    'mes' => $data['mes'],
                    'año' => $data['anio']
                ];
                
                if ($this->libroModel->updateLibro($id, $modelData)) {
                    $_SESSION['message'] = 'Libro actualizado exitosamente';
                    $_SESSION['message_type'] = 'success';
                    header('Location: index.php?controller=libro&action=show&id=' . $id);
                    exit;
                } else {
                    $errors[] = 'Error al actualizar el libro';
                }
            }
            
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header('Location: index.php?controller=libro&action=edit&id=' . $id);
            exit;
        }
    }
    
    // Eliminar libro
    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=libro&action=index');
            exit;
        }
        
        if ($this->libroModel->deleteLibro($id)) {
            $_SESSION['message'] = 'Libro eliminado exitosamente';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error al eliminar el libro';
            $_SESSION['message_type'] = 'error';
        }
        
        header('Location: index.php?controller=libro&action=index');
        exit;
    }
    
public function assignSignatories() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    error_log("🎯 assignSignatories INICIADO");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // DEBUG de lo que llega
        error_log("📨 POST recibido: " . print_r($_POST, true));
        
        $libroId = isset($_POST['libro_id']) ? (int)$_POST['libro_id'] : 0;
        $firmantes = isset($_POST['firmantes']) ? (array)$_POST['firmantes'] : [];
        
        error_log("📖 Libro ID: " . $libroId);
        error_log("👥 Firmantes: " . implode(', ', $firmantes));

        // Validaciones
        if ($libroId <= 0) {
            error_log("❌ Libro ID inválido");
            $_SESSION['message'] = 'Error: ID de libro inválido';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=libro&action=index');
            exit;
        }
        
        if (empty($firmantes)) {
            error_log("❌ No hay firmantes");
            $_SESSION['message'] = 'Error: Debe seleccionar al menos un firmante';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?controller=libro&action=show&id=' . $libroId);
            exit;
        }

        // Procesar
        try {
            error_log("🚀 Llamando al modelo...");
            $result = $this->firmaModel->asignarFirmantesALibro($libroId, $firmantes);
            
            if ($result) {
                error_log("✅ Firmantes asignados correctamente");
                $_SESSION['message'] = 'Firmantes asignados exitosamente: ' . implode(', ', $firmantes);
                $_SESSION['message_type'] = 'success';
            } else {
                error_log("❌ Error en el modelo");
                $_SESSION['message'] = 'Error al asignar firmantes';
                $_SESSION['message_type'] = 'error';
            }
        } catch (Exception $e) {
            error_log("💥 Excepción: " . $e->getMessage());
            $_SESSION['message'] = 'Error: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }

        // REDIRECCIÓN CRÍTICA - Verificar que el libroId sea correcto
        error_log("📍 Redirigiendo a: show&id=" . $libroId);
        
        $redirectUrl = 'index.php?controller=libro&action=show&id=' . $libroId;
        error_log("🔗 URL de redirección: " . $redirectUrl);
        
        header('Location: ' . $redirectUrl);
        exit;
        
    } else {
        error_log("⚠️ Acceso por GET");
        header('Location: index.php?controller=libro&action=index');
        exit;
    }
}
    // Cambiar estado de firma
    public function updateSignatureStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firmaId = $_POST['firma_id'] ?? null;
            $nuevoEstado = $_POST['nuevo_estado'] ?? 'firmado';
            
            if ($firmaId) {
                if ($this->firmaModel->updateEstadoFirma($firmaId, $nuevoEstado)) {
                    $_SESSION['message'] = 'Estado de firma actualizado exitosamente';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Error al actualizar el estado de la firma';
                    $_SESSION['message_type'] = 'error';
                }
            }
            
            // Redirigir de vuelta a la página anterior
            $redirect = $_POST['redirect'] ?? 'index.php?controller=libro&action=index';
            header('Location: ' . $redirect);
            exit;
        }
    }
    
    // Firmar (cambiar estado a firmado)
    public function sign() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firmaId = $_POST['firma_id'] ?? null;
            $libroId = $_POST['libro_id'] ?? null;
            
            if ($firmaId) {
                if ($this->firmaModel->updateEstadoFirma($firmaId, 'firmado')) {
                    $_SESSION['message'] = 'Firma registrada exitosamente';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Error al registrar la firma';
                    $_SESSION['message_type'] = 'error';
                }
            }
            
            // Redirigir según el contexto
            if ($libroId) {
                header('Location: index.php?controller=libro&action=show&id=' . $libroId);
            } else {
                header('Location: index.php?controller=libro&action=index');
            }
            exit;
        }
        
        // Fallback para GET requests (compatibilidad)
        $firmaId = $_GET['firma_id'] ?? null;
        $libroId = $_GET['libro_id'] ?? null;
        
        if ($firmaId) {
            if ($this->firmaModel->updateEstadoFirma($firmaId, 'firmado')) {
                $_SESSION['message'] = 'Firma registrada exitosamente';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Error al registrar la firma';
                $_SESSION['message_type'] = 'error';
            }
        }
        
        // Redirigir según el contexto
        if ($libroId) {
            header('Location: index.php?controller=libro&action=show&id=' . $libroId);
        } else {
            header('Location: index.php?controller=libro&action=index');
        }
        exit;
    }

    
}