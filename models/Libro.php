<?php

class Libro {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllLibros() {
        try {
            $sql = "
                SELECT 
                    l.*,
                    'Sistema' as responsable,
                    COUNT(f.id) as total_firmas,
                    SUM(CASE WHEN f.estado = 'firmado' THEN 1 ELSE 0 END) as firmas_completadas,
                    SUM(CASE WHEN f.estado = 'pendiente' THEN 1 ELSE 0 END) as firmas_pendientes
                FROM libros l
                LEFT JOIN firmas f ON l.id = f.libro_id
                GROUP BY l.id
                ORDER BY l.id DESC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAllLibros: " . $e->getMessage());
            return [];
        }
    }

    public function getAllWithFirmantes() {
        try {
            $sql = "
                SELECT 
                    l.*,
                    GROUP_CONCAT(fr.nombre SEPARATOR ', ') AS firmantes,
                    COUNT(fi.id) AS total_firmas,
                    SUM(CASE WHEN fi.estado = 'firmado' THEN 1 ELSE 0 END) AS firmas_completadas,
                    SUM(CASE WHEN fi.estado = 'pendiente' THEN 1 ELSE 0 END) AS firmas_pendientes
                FROM libros l
                LEFT JOIN firmas fi ON fi.libro_id = l.id
                LEFT JOIN firmantes fr ON fi.firmante_id = fr.id
                GROUP BY l.id
                ORDER BY l.id DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAllWithFirmantes: " . $e->getMessage());
            return [];
        }
    }
    
    
    public function getLibroById($id) {
        try {
            $sql = "
                SELECT 
                    l.*,
                    'Sistema' as responsable
                FROM libros l
                WHERE l.id = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getLibroById: " . $e->getMessage());
            return false;
        }
    }
    
    public function existeNumeroReferencia($numeroReferencia, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM libros WHERE numero_referencia = ?";
            $params = [$numeroReferencia];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en existeNumeroReferencia: " . $e->getMessage());
            return false;
        }
    }
    
    public function createLibro($data) {
        try {
            $sql = "
                INSERT INTO libros (numero_referencia, titulo, descripcion, mes, anio, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['numero_referencia'],
                $data['titulo'],
                $data['descripcion'],
                $data['mes'],
                $data['anio'],
                $data['created_by'] ?? $_SESSION['user_id'] ?? 1
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en createLibro: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateLibro($id, $data) {
        try {
            $sql = "
                UPDATE libros 
                SET numero_referencia = ?, titulo = ?, descripcion = ?, mes = ?, año = ?, updated_at = NOW()
                WHERE id = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['numero_referencia'],
                $data['titulo'],
                $data['descripcion'],
                $data['mes'],
                $data['año'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en updateLibro: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteLibro($id) {
        try {
            // Primero eliminar las firmas asociadas
            $stmt = $this->pdo->prepare("DELETE FROM firmas WHERE libro_id = ?");
            $stmt->execute([$id]);
            
            // Luego eliminar el libro
            $stmt = $this->pdo->prepare("DELETE FROM libros WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en deleteLibro: " . $e->getMessage());
            return false;
        }
    }
    
    public function getLibrosByUser($userId) {
        try {
            $sql = "
                SELECT 
                    l.*,
                    u.nombre as responsable,
                    COUNT(f.id) as total_firmas,
                    SUM(CASE WHEN f.estado = 'firmado' THEN 1 ELSE 0 END) as firmas_completadas,
                    SUM(CASE WHEN f.estado = 'pendiente' THEN 1 ELSE 0 END) as firmas_pendientes
                FROM libros l
                LEFT JOIN users u ON l.created_by = u.id
                LEFT JOIN firmas f ON l.id = f.libro_id
                WHERE l.created_by = ?
                GROUP BY l.id
                ORDER BY l.created_at DESC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getLibrosByUser: " . $e->getMessage());
            return [];
        }
    }
}