<?php

require_once __DIR__ . '/../Config/conexion.php';

class DevolucionModelo {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    /**
     * Obtiene todas las devoluciones activas con nombre de producto.
     * @return array
     */
    public function obtenerTodas() {
        $sql = "SELECT d.*, p.nombre AS nombre_producto 
                FROM devoluciones d
                LEFT JOIN productos p ON d.producto_id = p.id
                WHERE d.estado = 1
                ORDER BY d.fecha DESC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener devoluciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * @param array
     * @return bool
     */
    public function insertar($data) {
        $sql = "INSERT INTO devoluciones (cliente, venta_id, producto_id, cantidad, fecha, motivo, estado)
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['cliente'],
                $data['venta_id'],
                $data['producto_id'],
                $data['cantidad'],
                $data['fecha'],
                $data['motivo']
            ]);
        } catch (PDOException $e) {
            error_log("Error al insertar devolución: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @param int
     * @return bool
     */
    public function eliminar($id) {
        $sql = "UPDATE devoluciones SET estado = 0 WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar devolución: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM devoluciones WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
