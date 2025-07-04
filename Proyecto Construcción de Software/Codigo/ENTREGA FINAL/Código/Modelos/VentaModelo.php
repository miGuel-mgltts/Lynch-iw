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
        $sql = "SELECT v.id, v.cliente, v.fecha, v.total
                FROM ventas v
                ORDER BY v.id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ventas as &$venta) {
            $sqlDetalles = "SELECT p.nombre, dv.cantidad, dv.precio_unitario, dv.total_linea
                            FROM detalle_ventas dv
                            JOIN productos p ON p.id = dv.producto_id
                            WHERE dv.venta_id = ?";
            $stmtDetalle = $this->pdo->prepare($sqlDetalles);
            $stmtDetalle->execute([$venta['id']]);
            $venta['detalles'] = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
        }

        return $ventas;
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
    
    public function obtenerVentaPorId($id) {
        $sql = "SELECT * FROM ventas WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallePorVenta($venta_id) {
        $sql = "SELECT dv.*, p.nombre AS producto_nombre 
                FROM detalle_ventas dv
                JOIN productos p ON p.id = dv.producto_id
                WHERE dv.venta_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$venta_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarVenta($id, $cliente, $fecha, $total) {
        $sql = "UPDATE ventas SET cliente = ?, fecha = ?, total = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$cliente, $fecha, $total, $id]);
    }

    public function eliminarDetalles($venta_id) {
        $sql = "DELETE FROM detalle_ventas WHERE venta_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$venta_id]);
    }

    public function obtenerVentasConDetalle() {
        // Obtener ventas
        $sqlVentas = "SELECT id, cliente, fecha FROM ventas ";
        $stmtVentas = $this->pdo->prepare($sqlVentas);
        $stmtVentas->execute();
        $ventas = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ventas as &$venta) {
            $sqlDetalles = "SELECT dv.producto_id, p.nombre AS nombre_producto, dv.cantidad 
                            FROM detalle_ventas dv 
                            INNER JOIN productos p ON p.id = dv.producto_id 
                            WHERE dv.venta_id = ?";
            $stmtDetalles = $this->pdo->prepare($sqlDetalles);
            $stmtDetalles->execute([$venta['id']]);
            $venta['productos'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);
        }

        return $ventas;
    }

    public function restarCantidadDetalleVenta($venta_id, $producto_id, $cantidad) {
        $sql = "UPDATE detalle_ventas 
                SET cantidad = cantidad - :cantidad 
                WHERE venta_id = :venta_id AND producto_id = :producto_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':cantidad' => $cantidad,
            ':venta_id' => $venta_id,
            ':producto_id' => $producto_id
        ]);
    }

    public function aumentarStockProducto($producto_id, $cantidad) {
        $sql = "UPDATE productos 
                SET stock_inicial = stock_inicial + :cantidad 
                WHERE id = :producto_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':cantidad' => $cantidad,
            ':producto_id' => $producto_id
        ]);
    }

    // Sumar cantidad en detalle_ventas (revertir la resta previa)
    public function sumarCantidadDetalleVenta($ventaId, $productoId, $cantidad) {
        $sql = "UPDATE detalle_ventas 
                SET cantidad = cantidad + ? 
                WHERE venta_id = ? AND producto_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([(int)$cantidad, $ventaId, $productoId]);
    }

    // Disminuir stock en productos (restar la cantidad que ya no está devuelta)
    public function disminuirStockProducto($productoId, $cantidad) {
        $sql = "UPDATE productos 
                SET stock_inicial= stock_inicial - ? 
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([(int)$cantidad, $productoId]);
    }

}
