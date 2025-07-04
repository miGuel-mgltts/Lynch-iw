<?php
require_once __DIR__ . '/../config/conexion.php';

class ProductoModelo {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function obtenerTodos() {
        $sql = "SELECT * FROM productos WHERE estado = 1";
        $productos = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $productos ?: [];
    }

    public function insertar($data) {
        $sql = "INSERT INTO productos (codigo, nombre, descripcion, categoria, precio_venta, precio_compra, stock_inicial, stock_minimo, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function obtenerPorCodigo($codigo) {
        $sql = "SELECT * FROM productos WHERE codigo = ? AND estado = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$codigo]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        return $producto ?: false;
    }

    public function actualizar($data) {
        $sql = "UPDATE productos SET nombre=?, descripcion=?, categoria=?, precio_venta=?, precio_compra=?, stock_inicial=?, stock_minimo=? WHERE codigo=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }


    public function eliminar($codigo) {
        $sql = "UPDATE productos SET estado = 0 WHERE codigo = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$codigo]);
    }

    public function actualizarStock($producto_id, $cantidad_vendida) {
        $sql = "UPDATE productos SET stock_inicial = stock_inicial - ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$cantidad_vendida, $producto_id]);
    }

    public function obtenerPorNombre($nombre) {
        $sql = "SELECT * FROM productos WHERE nombre = ? AND estado = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$nombre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
