<?php

class Firmante
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllFirmantes()
    {
        try {
            $sql = "
                SELECT 
                    f.*,
                    COUNT(fi.id) as total_firmas,
                    SUM(CASE WHEN fi.estado = 'firmado' THEN 1 ELSE 0 END) as firmas_completadas,
                    SUM(CASE WHEN fi.estado = 'pendiente' THEN 1 ELSE 0 END) as firmas_pendientes
                FROM firmantes f
                LEFT JOIN firmas fi ON f.id = fi.firmante_id
                WHERE f.activo = 1
                GROUP BY f.id
                ORDER BY f.nombre ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getAllFirmantes: " . $e->getMessage());
            return [];
        }
    }

    public function getFirmanteById($id)
    {
        try {
            $sql = "SELECT * FROM firmantes WHERE id = ? AND activo = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getFirmanteById: " . $e->getMessage());
            return false;
        }
    }

    public function getFirmanteByEmail($email)
    {
        try {
            $sql = "SELECT * FROM firmantes WHERE email = ? AND activo = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getFirmanteByEmail: " . $e->getMessage());
            return false;
        }
    }

    public function existeEmail($email, $excludeId = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM firmantes WHERE email = ? AND activo = 1";
            $params = [$email];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en existeEmail: " . $e->getMessage());
            return false;
        }
    }

    public function createFirmante($data)
    {
        try {
            $sql = "
                INSERT INTO firmantes (nombre, cargo, email, telefono, departamento, activo, created_at)
                VALUES (?, ?, ?, ?, ?, 1, NOW())
            ";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['nombre'],
                $data['cargo'],
                $data['email'],
                $data['telefono'] ?? null,
                $data['departamento'] ?? null
            ]);

            if ($result) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en createFirmante: " . $e->getMessage());
            return false;
        }
    }

    public function updateFirmante($id, $data)
    {
        try {
            $sql = "
                UPDATE firmantes 
                SET nombre = ?, cargo = ?, email = ?, telefono = ?, departamento = ?, updated_at = NOW()
                WHERE id = ?
            ";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['nombre'],
                $data['cargo'],
                $data['email'],
                $data['telefono'] ?? null,
                $data['departamento'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error en updateFirmante: " . $e->getMessage());
            return false;
        }
    }

    public function deleteFirmante($id)
    {
        try {
            // Marcar como inactivo en lugar de eliminar físicamente
            $stmt = $this->pdo->prepare("UPDATE firmantes SET activo = 0, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en deleteFirmante: " . $e->getMessage());
            return false;
        }
    }

    public function activarFirmante($id)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE firmantes SET activo = 1, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error en activarFirmante: " . $e->getMessage());
            return false;
        }
    }

    public function searchFirmantes($query)
    {
        try {
            $searchTerm = "%" . $query . "%";
            $sql = "
                SELECT 
                    f.*,
                    COUNT(fi.id) as total_firmas,
                    SUM(CASE WHEN fi.estado = 'firmado' THEN 1 ELSE 0 END) as firmas_completadas,
                    SUM(CASE WHEN fi.estado = 'pendiente' THEN 1 ELSE 0 END) as firmas_pendientes
                FROM firmantes f
                LEFT JOIN firmas fi ON f.id = fi.firmante_id
                WHERE f.activo = 1 
                AND (f.nombre LIKE ? OR f.cargo LIKE ? OR f.email LIKE ? OR f.departamento LIKE ?)
                GROUP BY f.id
                ORDER BY f.nombre ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en searchFirmantes: " . $e->getMessage());
            return [];
        }
    }

    public function getFirmantesByDepartamento($departamento)
    {
        try {
            $sql = "
                SELECT * FROM firmantes 
                WHERE departamento = ? AND activo = 1
                ORDER BY nombre ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$departamento]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getFirmantesByDepartamento: " . $e->getMessage());
            return [];
        }
    }

    public function getDepartamentos()
    {
        try {
            $sql = "
                SELECT DISTINCT departamento 
                FROM firmantes 
                WHERE departamento IS NOT NULL AND departamento != '' AND activo = 1
                ORDER BY departamento ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error en getDepartamentos: " . $e->getMessage());
            return [];
        }
    }

    public function getEstadisticasFirmantes()
    {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_firmantes,
                    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as inactivos
                FROM firmantes
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en getEstadisticasFirmantes: " . $e->getMessage());
            return [
                'total_firmantes' => 0,
                'activos' => 0,
                'inactivos' => 0
            ];
        }
    }
}
