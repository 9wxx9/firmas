<?php

class Libro
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllLibros()
    {
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

    public function getAllWithFirmantes()
    {
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


    // En models/Libro.php

public function getLibrosPendientesPorUsuario($emailUsuario)
{
    $query = "SELECT DISTINCT 
                l.id,
                l.numero_referencia,
                l.titulo,
                l.descripcion,
                l.mes,
                l.año,
                l.dia,
                f.id as firma_id,
                f.orden,
                f.estado,
                f.fecha_firma,
                firm.nombre as firmante_nombre,
                firm.cargo as firmante_cargo,
                firm.departamento as firmante_departamento
            FROM libros l
            INNER JOIN firmas f ON l.id = f.libro_id
            INNER JOIN firmantes firm ON f.firmante_id = firm.id
            WHERE firm.email = :email
            AND f.estado = 'pendiente'
            AND firm.activo = 1
            ORDER BY l.created_at DESC, f.orden ASC";
    
    try {
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $emailUsuario, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en getLibrosPendientesPorUsuario: " . $e->getMessage());
        return [];
    }
}

      // NUEVOS MÉTODOS para el dashboard

     /**
     * Obtener los libros/diarios más recientes
     */
    public function obtenerRecientes($limit = 5)
    {
        try {
            $sql = "SELECT 
                        l.id,
                        l.numero_referencia,
                        l.titulo,
                        l.descripcion,
                        l.mes,
                        l.año as anio,
                        l.estado,
                        l.created_at,
                        l.escaneado,
                        COUNT(fi.id) as total_firmas,
                        COUNT(CASE WHEN fi.estado = 'firmado' THEN 1 END) as firmas_completadas
                    FROM libros l
                    LEFT JOIN firmas fi ON l.id = fi.libro_id
                    GROUP BY l.id
                    ORDER BY l.created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerRecientes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener actividad reciente del sistema
     */
    public function obtenerActividadReciente($limit = 10)
    {
        try {
            $sql = "SELECT 
                        'firma' as tipo,
                        fi.id,
                        fi.fecha_firma as created_at,
                        fi.estado,
                        l.titulo as diario_titulo,
                        l.numero_referencia,
                        f.nombre as firmante_nombre,
                        CASE 
                            WHEN fi.estado = 'firmado' THEN 'Firma completada'
                            ELSE 'Firma pendiente'
                        END as accion
                    FROM firmas fi
                    INNER JOIN libros l ON fi.libro_id = l.id
                    INNER JOIN firmantes f ON fi.firmante_id = f.id
                    WHERE fi.fecha_firma IS NOT NULL
                    
                    UNION ALL
                    
                    SELECT 
                        'libro' as tipo,
                        l.id,
                        l.created_at,
                        l.estado,
                        l.titulo as diario_titulo,
                        l.numero_referencia,
                        NULL as firmante_nombre,
                        'Diario registrado' as accion
                    FROM libros l
                    
                    ORDER BY created_at DESC
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerActividadReciente: " . $e->getMessage());
            return [];
        }
    }

    public function getLibroById($id)
    {
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

    public function getTotalLibros()
    {

        try {
            $sql = "SELECT 
            
            (SELECT COUNT(*) FROM libros) AS total_diarios,
            (SELECT COUNT(*) FROM usuarios) AS total_usuarios,
            (SELECT COUNT(*) FROM firmas WHERE estado = 'firmado') AS total_firmas_completas,
            (SELECT COUNT(*) FROM firmas WHERE estado = 'pendiente') AS total_firmas_pendientes
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getTotalDiarios: " . $e->getMessage());
            return false;
        }
    }

    public function existeNumeroReferencia($numeroReferencia, $excludeId = null)
    {
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

    public function createLibro($data)
    {
        try {
            $sql = "
                INSERT INTO libros (numero_referencia, titulo, descripcion, mes, año, dia, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['numero_referencia'],
                $data['titulo'],
                $data['descripcion'],
                $data['mes'],
                $data['año'],
                $data['dia'],
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

    public function updateLibro($id, $data)
    {
        try {
            $sql = "
                UPDATE libros 
                SET numero_referencia = ?, titulo = ?, descripcion = ?, mes = ?, año = ?, dia = ?, updated_at = NOW()
                WHERE id = ?
            ";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['numero_referencia'],
                $data['titulo'],
                $data['descripcion'],
                $data['mes'],
                $data['año'],
                $data['dia'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en updateLibro: " . $e->getMessage());
            return false;
        }
    }

    public function deleteLibro($id)
    {
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

    public function getLibrosByUser($userId)
    {
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
    // ... existing code ...

    public function updateEscaneado($id, $escaneado)
    {
        try {
            $sql = "UPDATE libros SET escaneado = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$escaneado ? 1 : 0, $id]);
        } catch (PDOException $e) {
            error_log("Error en updateEscaneado: " . $e->getMessage());
            return false;
        }
    }
}
