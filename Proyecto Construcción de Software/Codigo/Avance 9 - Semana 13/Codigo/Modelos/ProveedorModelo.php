<?php

require_once __DIR__ . '/../Config/conexion.php'; 

class ProveedorModelo {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    /**     
     * @return array Un array de proveedores con el nombre del producto asociado.
     */
    public function obtenerTodos() {
        $sql = "SELECT p.*, prod.nombre AS nombre_producto
                FROM proveedores p
                LEFT JOIN productos prod ON p.id_producto = prod.id
                WHERE p.estado = 1
                ORDER BY p.nombre ASC";
        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener todos los proveedores: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Inserta un nuevo proveedor en la base de datos.
     * @param array 
     * @return bool 
     */
    public function insertar($data) {
        $sql = "INSERT INTO proveedores (cedula, ruc, nombre, telefono, direccion, correo, id_producto, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['cedula'],
                $data['ruc'],
                $data['nombre'],
                $data['telefono'],
                $data['direccion'],
                $data['correo'],
                $data['id_producto']
            ]);
        } catch (PDOException $e) {
            error_log("Error al insertar proveedor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un proveedor por su RUC, uniendo con la tabla de productos.
     * @param string 
     * @return array|false 
     */
    public function obtenerPorRuc($ruc) {
        $sql = "SELECT p.*, prod.nombre AS nombre_producto
                FROM proveedores p
                LEFT JOIN productos prod ON p.id_producto = prod.id
                WHERE p.ruc = ? AND p.estado = 1";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$ruc]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Error al obtener proveedor por RUC: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza los datos de un proveedor existente.
     * @param array 
     * @return bool 
     */
    public function actualizar($data) {
        $sql = "UPDATE proveedores SET cedula=?, nombre=?, telefono=?, direccion=?, correo=?, id_producto=? WHERE ruc=?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['cedula'],
                $data['nombre'],
                $data['telefono'],
                $data['direccion'],
                $data['correo'],
                $data['id_producto'],
                $data['ruc']
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar proveedor: " . $e->getMessage());
            return false;
        }
    }

    /**
     *  proveedor cambiando su estado a 0 (inactivo).
     * @param string 
     * @return bool 
     */
    public function eliminar($ruc) {
        $sql = "UPDATE proveedores SET estado = 0 WHERE ruc = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$ruc]);
        } catch (PDOException $e) {
            error_log("Error al eliminar proveedor: " . $e->getMessage());
            return false;
        }
    }
}