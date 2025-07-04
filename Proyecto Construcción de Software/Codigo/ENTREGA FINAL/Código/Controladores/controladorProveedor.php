<?php

require_once __DIR__ . '/../Modelos/ProveedorModelo.php';
require_once __DIR__ . '/../Modelos/ProductoModelo.php';

class ControladorProveedor { 
    public $proveedores;
    public $productos; 
    public $mensaje = '';
    public $tipo_mensaje = ''; 
    public $proveedorEditar = null;

    private $proveedorModelo;
    private $productoModelo;

    public function __construct() {
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->proveedorModelo = new ProveedorModelo();
        $this->productoModelo = new ProductoModelo();

        // Inicializar datos para la vista
        $this->proveedores = $this->proveedorModelo->obtenerTodos();
        $this->productos = $this->productoModelo->obtenerTodos();

        // Manejar las acciones POST (agregar/actualizar/eliminar)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['accion'])) {
                $accion = $_POST['accion'];
                switch ($accion) {
                    case 'registrar':
                        $this->registrarProveedor($_POST);
                        break;
                    case 'actualizar':
                        $this->actualizarProveedor($_POST);
                        break;
                    case 'eliminar':
                        $this->eliminarProveedor($_POST['ruc'] ?? '');
                        break;
                }
            }
        } 
        // Manejar la acción GET para editar
        else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['editar_ruc'])) {
            $this->cargarProveedorParaEditar($_GET['editar_ruc']);
        }

        // Recuperar y limpiar mensajes de sesión
        if (isset($_SESSION['mensaje'])) {
            $this->mensaje = $_SESSION['mensaje'];
            $this->tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
            unset($_SESSION['mensaje']);
            unset($_SESSION['tipo_mensaje']);
        }
    }

    private function registrarProveedor($postData) {
        $data = [
            'cedula' => isset($postData['cedula']) && $postData['cedula'] !== '' ? htmlspecialchars(trim($postData['cedula'])) : null,
            'nombre' => isset($postData['nombre']) ? htmlspecialchars(trim($postData['nombre'])) : '',
            'ruc' => isset($postData['ruc']) ? htmlspecialchars(trim($postData['ruc'])) : '',
            'telefono' => isset($postData['telefono']) && $postData['telefono'] !== '' ? htmlspecialchars(trim($postData['telefono'])) : null,
            'direccion' => isset($postData['direccion']) && $postData['direccion'] !== '' ? htmlspecialchars(trim($postData['direccion'])) : null,
            'correo' => isset($postData['correo']) && $postData['correo'] !== '' ? filter_var($postData['correo'], FILTER_SANITIZE_EMAIL) : null,
            'id_producto' => isset($postData['id_producto']) && $postData['id_producto'] !== '' ? (int)$postData['id_producto'] : null
        ];

        if (empty($data['nombre']) || empty($data['ruc']) || is_null($data['id_producto'])) {
            $this->setMensaje("Error: Nombre, RUC y Producto Asociado son campos obligatorios.", "error");
            return;
        }
        if (!empty($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $this->setMensaje("Error: El formato del correo electrónico no es válido.", "error");
            return;
        }
        if ($this->proveedorModelo->obtenerPorRuc($data['ruc'])) {
            $this->setMensaje("Error: Ya existe un proveedor con el RUC " . $data['ruc'] . ".", "error");
            return;
        }

        if ($this->proveedorModelo->insertar($data)) {
            $this->setMensaje("Proveedor registrado exitosamente.", "success");
        } else {
            $this->setMensaje("Error al registrar el proveedor.", "error");
        }
        // Recargar la lista de proveedores después de la acción
        $this->proveedores = $this->proveedorModelo->obtenerTodos();
    }

    private function actualizarProveedor($postData) {
        $data = [
            'cedula' => isset($postData['cedula']) && $postData['cedula'] !== '' ? htmlspecialchars(trim($postData['cedula'])) : null,
            'nombre' => isset($postData['nombre']) ? htmlspecialchars(trim($postData['nombre'])) : '',
            'ruc' => isset($postData['ruc_original']) ? htmlspecialchars(trim($postData['ruc_original'])) : '', 
            'telefono' => isset($postData['telefono']) && $postData['telefono'] !== '' ? htmlspecialchars(trim($postData['telefono'])) : null,
            'direccion' => isset($postData['direccion']) && $postData['direccion'] !== '' ? htmlspecialchars(trim($postData['direccion'])) : null,
            'correo' => isset($postData['correo']) && $postData['correo'] !== '' ? filter_var($postData['correo'], FILTER_SANITIZE_EMAIL) : null,
            'id_producto' => isset($postData['id_producto']) && $postData['id_producto'] !== '' ? (int)$postData['id_producto'] : null
        ];

        if (empty($data['nombre']) || empty($data['ruc']) || is_null($data['id_producto'])) {
            $this->setMensaje("Error: Nombre, RUC y Producto Asociado son campos obligatorios para la actualización.", "error");
            return;
        }
        if (!empty($data['correo']) && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $this->setMensaje("Error: El formato del correo electrónico no es válido.", "error");
            return;
        }

        if ($this->proveedorModelo->actualizar($data)) {
            $this->setMensaje("Proveedor actualizado exitosamente.", "success");
        } else {
            $this->setMensaje("Error al actualizar el proveedor.", "error");
        }
        // Recargar la lista de proveedores después de la acción
        $this->proveedores = $this->proveedorModelo->obtenerTodos();
        $this->proveedorEditar = null; // Limpiar el estado de edición
    }

    private function eliminarProveedor($ruc) {
        if (empty($ruc)) {
            $this->setMensaje("Error: RUC del proveedor no especificado para eliminar.", "error");
            return;
        }
        if ($this->proveedorModelo->eliminar($ruc)) {
            $this->setMensaje("Proveedor eliminado (desactivado) exitosamente.", "success");
        } else {
            $this->setMensaje("Error al eliminar el proveedor.", "error");
        }
        
        $this->proveedores = $this->proveedorModelo->obtenerTodos();
    }

    private function cargarProveedorParaEditar($ruc) {
        $this->proveedorEditar = $this->proveedorModelo->obtenerPorRuc($ruc);
        if (!$this->proveedorEditar) {
            $this->setMensaje("Proveedor no encontrado para editar.", "error");
        }
    }

    private function setMensaje($msg, $type) {
        $_SESSION['mensaje'] = $msg;
        $_SESSION['tipo_mensaje'] = $type;
    }
}