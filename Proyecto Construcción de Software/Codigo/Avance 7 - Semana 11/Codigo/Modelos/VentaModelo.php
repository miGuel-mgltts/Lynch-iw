<?php
require_once __DIR__ . '/../config/conexion.php';

class VentaModelo {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function insertarVenta($cliente, $fecha, $total) {
        $sql = "INSERT INTO ventas (cliente, fecha, total) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cliente, $fecha, $total]);
        return $this->pdo->lastInsertId();
    }

    public function insertarDetalle($venta_id, $producto_id, $cantidad, $precio_unitario, $total_linea) {
        $sql = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, total_linea)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$venta_id, $producto_id, $cantidad, $precio_unitario, $total_linea]);
    }

    public function obtenerTodasLasVentas() {
        $sql = " SELECT 
                v.id,
                v.cliente,
                v.fecha,
                v.total,
                GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos
            FROM ventas v
            JOIN detalle_ventas dv ON v.id = dv.venta_id
            JOIN productos p ON p.id = dv.producto_id
            GROUP BY v.id, v.cliente, v.fecha, v.total
            ORDER BY v.id DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarVenta($idVenta) {
        // Obtener los productos y cantidades de la venta
        $sql = "SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idVenta]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolver stock a cada producto
        foreach ($detalles as $detalle) {
            $sql = "UPDATE productos SET stock_inicial = stock_inicial + ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$detalle['cantidad'], $detalle['producto_id']]);
        }

        // Eliminar detalles
        $sql = "DELETE FROM detalle_ventas WHERE venta_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idVenta]);

        // Eliminar venta
        $sql = "DELETE FROM ventas WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$idVenta]);
    }       


}
