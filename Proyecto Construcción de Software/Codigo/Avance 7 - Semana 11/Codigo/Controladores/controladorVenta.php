<?php
require_once __DIR__ . '/../modelos/ProductoModelo.php';
require_once __DIR__ . '/../modelos/VentaModelo.php';

class ControladorVenta {
    public $productos = [];
    public $mensaje = '';
    public $ventas = [];

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
                    case 'eliminar':
                        $this->eliminar();
                        break;
                }
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

        // Si llegamos aquí sin errores, mensaje de éxito
        $this->mensaje = "Venta registrada correctamente";

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

}