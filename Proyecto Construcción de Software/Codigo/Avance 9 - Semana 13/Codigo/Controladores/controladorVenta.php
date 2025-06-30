<?php
require_once __DIR__ . '/../modelos/ProductoModelo.php';
require_once __DIR__ . '/../modelos/VentaModelo.php';

class ControladorVenta {
    public $productos = [];
    public $mensaje = '';
    public $ventas = [];
    public $ventaEditar = null;


    private $productoModelo;
    private $ventaModelo;

    public function __construct() {
        $this->productoModelo = new ProductoModelo();
        $this->ventaModelo = new VentaModelo();

        $this->productos = $this->productoModelo->obtenerTodos();

        $this->ventas = $this->ventaModelo->obtenerTodasLasVentas();

        // Procesar POST para registrar o actualizar o eliminar
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['accion'])) {
                switch ($_POST['accion']) {
                    case 'registrar':
                        $this->registrar();
                        break;
                     case 'editar':
                        $this->editar(); // Nuevo método
                        break;
                    case 'eliminar':
                        $this->eliminar();
                        break;
                }
            }
        }

        // Cargar venta para edición
        if (isset($_GET['editar_venta_id'])) {
            $this->ventaEditar = $this->ventaModelo->obtenerVentaPorId($_GET['editar_venta_id']);
            if ($this->ventaEditar) {
                $this->ventaEditar['detalles'] = $this->ventaModelo->obtenerDetallePorVenta($_GET['editar_venta_id']);
            }
        }

    }

    public function mostrarFormulario() {
        include __DIR__ . '/../vista/venta_form.php';
    }

    // Procesa la venta enviada por POST
    public function registrar() {
        $cliente = trim($_POST['cliente'] ?? '');
        $fecha = trim($_POST['fecha'] ?? '');
        $totalGeneral = floatval($_POST['totalGeneral'] ?? 0);

        $productoss = $_POST['productos'] ?? [];
        $cantidades = $_POST['cantidades'] ?? [];
        $precios = $_POST['precios'] ?? [];
        $totales = $_POST['totales'] ?? [];

        if (!$cliente || !$fecha || empty($productoss)) {
            $this->mensaje = "Faltan datos para registrar la venta";
            return;
        }

        // Insertar venta general y obtener id insertado
        $idVenta = $this->ventaModelo->insertarVenta($cliente, $fecha, $totalGeneral);

        if (!$idVenta) {
            $this->mensaje = "Error al registrar la venta";
            return;
        }

        // Recorrer cada producto vendido para insertar detalle y actualizar stock
        foreach ($productoss as $index => $nombreProducto) {
            $cantidad = (int)$cantidades[$index];
            $precio = (float)$precios[$index];
            $totalLinea = (float)$totales[$index];

            $productos = $this->productoModelo->obtenerPorNombre($nombreProducto);
            if (!$productos) continue;  // Ignorar si no existe producto

            // Validar stock
            if ($productos['stock_inicial'] < $cantidad) {
                $this->mensaje = "Stock insuficiente para el producto: {$nombreProducto}";
                return;
            }

            $producto_id = $productos['id'];

            // Insertar detalle de venta
            $insertDetalle = $this->ventaModelo->insertarDetalle($idVenta, $producto_id, $cantidad, $precio, $totalLinea);
            if (!$insertDetalle) {
                $this->mensaje = "Error al registrar el detalle para producto: {$nombreProducto}";
                return;
            }

            // Actualizar stock restando la cantidad vendida
            $updateStock = $this->productoModelo->actualizarStock($producto_id, $cantidad);
            if (!$updateStock) {
                $this->mensaje = "Error al actualizar stock para producto: {$nombreProducto}";
                return;
            }
        }

            $_SESSION['mensaje'] = "Venta registrada correctamente";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;

    }

    public function eliminar() {
        $idVenta = $_POST['id_venta'] ?? null;
        if ($idVenta) {
            $resultado = $this->ventaModelo->eliminarVenta($idVenta);
            if ($resultado) {
                $_SESSION['mensaje_exito'] = "Venta eliminada y stock restaurado.";
            } else {
                $_SESSION['mensaje_exito'] = "Error al eliminar la venta.";
            }
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    public function editar() {
        //OBTIENE VALORES
        $idVenta = intval($_POST['id_venta'] ?? 0);
        $cliente = trim($_POST['cliente'] ?? '');
        $fecha = trim($_POST['fecha'] ?? '');
        $totalGeneral = floatval($_POST['totalGeneral'] ?? 0);

        $productos = $_POST['productos'] ?? [];
        $cantidades = $_POST['cantidades'] ?? [];
        $precios = $_POST['precios'] ?? [];
        $totales = $_POST['totales'] ?? [];

        if (!$idVenta || !$cliente || !$fecha || empty($productos)) {
            $this->mensaje = "Faltan datos para actualizar la venta.";
            return;
        }

        // Restaurar stock original
        $ventaActual = $this->ventaModelo->obtenerDetallePorVenta($idVenta);
        foreach ($ventaActual as $detalle) {
            $this->productoModelo->actualizarStock($detalle['producto_id'], $detalle['cantidad']);
        }

        // Eliminar detalles actuales
        $this->ventaModelo->eliminarDetalles($idVenta);

        // Actualizar tabla ventas
        $this->ventaModelo->actualizarVenta($idVenta, $cliente, $fecha, $totalGeneral);

        // Insertar nuevos detalles y actualizar stock
        foreach ($productos as $i => $nombreProducto) {
            $cantidad = (int)$cantidades[$i];
            $precio = (float)$precios[$i];
            $total = (float)$totales[$i];

            $producto = $this->productoModelo->obtenerPorNombre($nombreProducto);
            if (!$producto) continue;

            if ($producto['stock_inicial'] < $cantidad) {
                $this->mensaje = "Stock insuficiente para el producto: $nombreProducto";
                return;
            }

            $this->ventaModelo->insertarDetalle($idVenta, $producto['id'], $cantidad, $precio, $total);
            $this->productoModelo->actualizarStock($producto['id'], $cantidad);
        }

        $_SESSION['mensaje_exito'] = "Venta actualizada correctamente";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    public function cargarVentaAEditar($id) {
        $venta = $this->ventaModelo->obtenerVentaPorId($id);
        $detalles = $this->ventaModelo->obtenerDetallePorVenta($id);

        if ($venta && $detalles) {
            $venta['detalles'] = $detalles;
            $this->ventaEditar = $venta;
        }
    }

}