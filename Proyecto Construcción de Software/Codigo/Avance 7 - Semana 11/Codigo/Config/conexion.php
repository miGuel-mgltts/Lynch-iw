<?php
class Conexion {
    public static function conectar() {
        $host = "localhost";
        $db = "inventario_db";
        $usuario = "root";
        $clave = "2004";

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $usuario, $clave);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
