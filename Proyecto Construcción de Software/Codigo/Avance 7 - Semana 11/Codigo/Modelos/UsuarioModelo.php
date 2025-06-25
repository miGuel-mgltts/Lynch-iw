<?php
require_once __DIR__ . '/../config/conexion.php';

class UsuarioModelo {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function registrar($nombre, $usuario, $clave) {
        $sql = "INSERT INTO usuarios (nombre, usuario, clave) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nombre, $usuario, password_hash($clave, PASSWORD_DEFAULT)]);
    }

    public function verificarLogin($usuario, $clave) {
        $sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($clave, $user['clave'])) {
            return $user;
        }
        return false;
    }
}
