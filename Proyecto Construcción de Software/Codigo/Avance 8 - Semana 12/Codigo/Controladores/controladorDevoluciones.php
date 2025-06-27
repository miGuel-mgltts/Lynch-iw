<?php
require_once __DIR__ . '/../Modelos/VentaModelo.php';
require_once __DIR__ . '/../Modelos/DevolucionModelo.php';

class ControladorDevoluciones {
    public $clientesVentasProductos = [];
    public $mensaje = '';
    public $tipo_mensaje = '';
    public $devoluciones;

    private $ventaModelo;
    private $devolucionModelo;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->ventaModelo = new VentaModelo();
        $this->devolucionModelo = new DevolucionModelo();

        // Preparar estructura cliente->ventas->productos
        $this->clientesVentasProductos = $this->prepararClientesVentasProductos();
        $this->devoluciones = $this->devolucionModelo->obtenerTodas();

        // // Registrar devolución si llega POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
            switch ($_POST['accion']) {
                case 'registrar':
                    $this->registrarDevolucion($_POST);
                    break;
                case 'eliminar':
                    $this->eliminarDevolucion($_POST['id'] ?? null);
                    break;
            }
        }

    }

    private function prepararClientesVentasProductos() {
        $ventasConDetalle = $this->ventaModelo->obtenerVentasConDetalle();
        $estructura = [];

        foreach ($ventasConDetalle as $venta) {
            $cliente = $venta['cliente'];
            if (!isset($estructura[$cliente])) {
                $estructura[$cliente] = ['ventas' => []];
            }

            $estructura[$cliente]['ventas'][] = [
                'id' => $venta['id'],
                'fecha' => $venta['fecha'],
                'productos' => array_map(function ($producto) {
                    return [
                        'producto_id' => $producto['producto_id'] ?? $producto['id'] ?? null,
                        'nombre_producto' => $producto['nombre_producto'] ?? $producto['nombre'] ?? 'Sin nombre'
                    ];
                }, $venta['productos'])
            ];
        }

        return $estructura;
    }


    private function registrarDevolucion($post) {
           error_log("POST recibido: " . print_r($post, true));

        $data = [
            'cliente' => trim($post['cliente'] ?? ''),
            'venta_id' => $post['venta_id'] ?? null,
            'producto_id' => $post['producto_id'] ?? null,
            'cantidad' => isset($post['cantidad']) ? (int)$post['cantidad'] : 1,
            'fecha' => $post['fecha'] ?? date('Y-m-d'),
            'motivo' => trim($post['motivo'] ?? '')
        ];

        if (empty($data['cliente']) || empty($data['venta_id']) || empty($data['producto_id']) || empty($data['motivo'])) {
            $this->setMensaje('Faltan datos obligatorios', 'error');
            return;
        }
        error_log("Datos para insertar: " . print_r($data, true));


        if ($this->devolucionModelo->insertar($data)) {

            // Actualizar detalle_ventas para restar la cantidad devuelta
             $this->ventaModelo->restarCantidadDetalleVenta($data['venta_id'], $data['producto_id'], $data['cantidad']);
    
            // Actualizar stock producto sumando la cantidad devuelta
            $this->ventaModelo->aumentarStockProducto($data['producto_id'], $data['cantidad']);


            $this->setMensaje('Devolución registrada correctamente', 'success');
            $this->devoluciones = $this->devolucionModelo->obtenerTodas();
        } else {
            $this->setMensaje('Error al registrar la devolución', 'error');
        }
    }

    private function eliminarDevolucion($id) {

         $devolucion = $this->devolucionModelo->obtenerPorId($id);

        if ($id && $this->devolucionModelo->eliminar($id)) {

        // Sumar cantidad eliminada a detalle_ventas (revertir la resta previa)
        $this->ventaModelo->sumarCantidadDetalleVenta($devolucion['venta_id'], $devolucion['producto_id'], $devolucion['cantidad']);

        // Disminuir stock producto (porque la devolución se anuló)
        $this->ventaModelo->disminuirStockProducto($devolucion['producto_id'], $devolucion['cantidad']);


            $this->setMensaje('Devolución eliminada correctamente', 'success');
            $this->devoluciones = $this->devolucionModelo->obtenerTodas();
        } else {
            $this->setMensaje('No se pudo eliminar la devolución', 'error');
        }
    }

    private function setMensaje($msg, $type) {
        $_SESSION['mensaje'] = $msg;
        $_SESSION['tipo_mensaje'] = $type;
        $this->mensaje = $msg;
        $this->tipo_mensaje = $type;
    }
}
