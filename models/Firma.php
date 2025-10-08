<?php

class Firma {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getFirmasByLibro($libroId) {
        try {
            $sql = "
                SELECT 
                    f.*,
                    fr.nombre as firmante_nombre,
                    fr.cargo as firmante_cargo,
                    fr.email as firmante_email,
                    fr.departamento as departamento
                FROM firmas f
                LEFT JOIN firmantes fr ON f.firmante_id = fr.id
                WHERE f.libro_id = ?
                ORDER BY f.orden ASC, f.created_at ASC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$libroId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getFirmasByLibro: " . $e->getMessage());
            return [];
        }
    }
    
    public function getFirmaById($id) {
        try {
            $sql = "
                SELECT 
                    f.*,
                    fr.nombre as firmante_nombre,
                    fr.cargo as firmante_cargo,
                    fr.email as firmante_email,
                    l.titulo as libro_titulo,
                    l.numero_referencia as libro_referencia
                FROM firmas f
                LEFT JOIN firmantes fr ON f.firmante_id = fr.id
                LEFT JOIN libros l ON f.libro_id = l.id
                WHERE f.id = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getFirmaById: " . $e->getMessage());
            return false;
        }
    }
    
    public function createFirma($data) {
        try {
            $sql = "
                INSERT INTO firmas (libro_id, firmante_id, estado, orden, observaciones, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['libro_id'],
                $data['firmante_id'],
                $data['estado'] ?? 'pendiente',
                $data['orden'] ?? 1,
                $data['observaciones'] ?? null
            ]);
            
            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en createFirma: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateFirma($id, $data) {
        try {
            $sql = "
                UPDATE firmas 
                SET estado = ?, observaciones = ?, fecha_firma = ?, updated_at = NOW()
                WHERE id = ?
            ";
            
            $fechaFirma = null;
            if (isset($data['estado']) && $data['estado'] === 'firmado') {
                $fechaFirma = date('Y-m-d H:i:s');
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['estado'],
                $data['observaciones'] ?? null,
                $fechaFirma,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en updateFirma: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteFirma($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM firmas WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en deleteFirma: " . $e->getMessage());
            return false;
        }
    }
    
    public function getFirmasPendientesByFirmante($firmanteId) {
        try {
            $sql = "
                SELECT 
                    f.*,
                    l.titulo as libro_titulo,
                    l.numero_referencia as libro_referencia,
                    l.descripcion as libro_descripcion
                FROM firmas f
                INNER JOIN libros l ON f.libro_id = l.id
                WHERE f.firmante_id = ? AND f.estado = 'pendiente'
                ORDER BY f.created_at ASC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$firmanteId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getFirmasPendientesByFirmante: " . $e->getMessage());
            return [];
        }
    }
    
    public function getEstadisticasFirmas() {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_firmas,
                    SUM(CASE WHEN estado = 'firmado' THEN 1 ELSE 0 END) as firmadas,
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazadas
                FROM firmas
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEstadisticasFirmas: " . $e->getMessage());
            return [
                'total_firmas' => 0,
                'firmadas' => 0,
                'pendientes' => 0,
                'rechazadas' => 0
            ];
        }
    }
    
    public function marcarComoFirmado($id, $observaciones = null) {
        return $this->updateFirma($id, [
            'estado' => 'firmado',
            'observaciones' => $observaciones
        ]);
    }
    
    public function marcarComoRechazado($id, $observaciones = null) {
        return $this->updateFirma($id, [
            'estado' => 'rechazado',
            'observaciones' => $observaciones
        ]);
    }
    
    public function getProximoOrden($libroId) {
        try {
            $sql = "SELECT COALESCE(MAX(orden), 0) + 1 as proximo_orden FROM firmas WHERE libro_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$libroId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['proximo_orden'] ?? 1;
        } catch (PDOException $e) {
            error_log("Error en getProximoOrden: " . $e->getMessage());
            return 1;
        }
    }
    
    public function asignarFirmantesALibro($libroId, $firmantes) {
        try {
            $this->pdo->beginTransaction();
            
            // Eliminar firmantes existentes del libro
            $stmt = $this->pdo->prepare("DELETE FROM firmas WHERE libro_id = ?");
            $stmt->execute([$libroId]);
            
            // Asignar nuevos firmantes
            $orden = 1;
            foreach ($firmantes as $firmanteId) {
                $sql = "
                    INSERT INTO firmas (libro_id, firmante_id, estado, orden, created_at)
                    VALUES (?, ?, 'pendiente', ?, NOW())
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$libroId, $firmanteId, $orden]);
                $orden++;
            }
            
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error en asignarFirmantesALibro: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateEstadoFirma($firmaId, $nuevoEstado) {
        try {
            $fechaFirma = null;
            if ($nuevoEstado === 'firmado') {
                $fechaFirma = date('Y-m-d H:i:s');
            }
            
            $sql = "
                UPDATE firmas 
                SET estado = ?, fecha_firma = ?, updated_at = NOW()
                WHERE id = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nuevoEstado, $fechaFirma, $firmaId]);
        } catch (PDOException $e) {
            error_log("Error en updateEstadoFirma: " . $e->getMessage());
            return false;
        }
    }
}