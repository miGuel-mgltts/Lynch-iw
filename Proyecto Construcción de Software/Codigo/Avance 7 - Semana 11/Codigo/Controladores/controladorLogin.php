<?php
session_start();
require_once __DIR__ . '/../Modelos/UsuarioModelo.php';

$modelo = new UsuarioModelo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $usuario = $_POST['usuario'];
        $clave = $_POST['clave'];

        $user = $modelo->verificarLogin($usuario, $clave);
        if ($user) {
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            header('Location: ../vistas/principal.php');
            exit;
        } else {
            $_SESSION['error'] = 'Usuario o contraseña incorrectos.';
            header('Location: ../vistas/login.php');
            exit;
        }
    }

    if (isset($_POST['registrar'])) {
        $nombre = $_POST['nombre'];
        $usuario = $_POST['usuario'];
        $clave = $_POST['clave'];

        if ($modelo->registrar($nombre, $usuario, $clave)) {
            $_SESSION['mensaje'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
        } else {
            $_SESSION['error'] = 'Error al registrar. Inténtalo de nuevo.';
        }
        header('Location: ../vistas/login.php');
        exit;
    }
}
