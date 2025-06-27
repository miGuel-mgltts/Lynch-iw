<?php
require_once __DIR__ . '/../Modelos/ProductoModelo.php';

class ControladorProductos {
    private $productoModelo;
    public $mensaje = "";
    public $productos = [];
    public $productoEditar = null;

    public function __construct() {
        $this->productoModelo = new ProductoModelo();

        // Procesar POST para registrar o actualizar
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['accion'])) {
                switch ($_POST['accion']) {
                    case 'registrar':
                        $this->registrar();
                        break;
                    case 'editar':
                        $this->editar();
                        break;
                    case 'eliminar':
                        $this->eliminar();
                        break;
                }
            }
        }

        // Cargar producto para edición (GET con codigo)
        if (isset($_GET['editar_codigo'])) {
            $this->productoEditar = $this->productoModelo->obtenerPorCodigo($_GET['editar_codigo']);
        }

        // Cargar todos los productos (o filtrados si quieres)
        $this->productos = $this->productoModelo->obtenerTodos();

        $this->aplicarFiltros();
    }

    private function registrar() {
        $codigo = $_POST['codigo'] ?? '';

        $productoExistente = $this->productoModelo->obtenerPorCodigo($codigo);
        if ($productoExistente) {
            $this->mensaje = "El código '$codigo' ya existe.";
            return;
        }
        
        $data = [
            $codigo,
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['categoria'],
            $_POST['precio_venta'],
            $_POST['precio_compra'],
            $_POST['stock_inicial'],
            $_POST['stock_minimo']
        ];
        if ($this->productoModelo->insertar($data)) {
            $this->mensaje = "Producto registrado correctamente.";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $this->mensaje = "Error al registrar el producto.";
        }
    }

    private function editar() {
        $codigo = $_POST['codigo'] ?? '';
        $data = [
            $_POST['nombre'],
            $_POST['descripcion'],
            $_POST['categoria'],
            $_POST['precio_venta'],
            $_POST['precio_compra'],
            $_POST['stock_inicial'],
            $_POST['stock_minimo'],
            $codigo
        ];
        if ($this->productoModelo->actualizar($data)) {
            $this->mensaje = "Producto actualizado correctamente.";
        } else {
            $this->mensaje = "Error al actualizar el producto.";
        }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
            
    }

    private function eliminar() {
        $codigo = $_POST['codigo'] ?? '';
        if ($this->productoModelo->eliminar($codigo)) {
            $this->mensaje = "Producto eliminado correctamente.";
        } else {
            $this->mensaje = "Error al eliminar el producto.";
        }
    }

    private function aplicarFiltros() {
        if (isset($_GET['filtro_codigo']) && $_GET['filtro_codigo'] !== '') {
            $codigo = strtolower($_GET['filtro_codigo']);
            $this->productos = array_filter($this->productos, function($p) use ($codigo) {
                return stripos($p['codigo'], $codigo) !== false;
            });
        }

        if (isset($_GET['filtro_categoria']) && $_GET['filtro_categoria'] !== '') {
            $categoria = $_GET['filtro_categoria'];
            $this->productos = array_filter($this->productos, function($p) use ($categoria) {
                return $p['categoria'] === $categoria;
            });
        }

        if (isset($_GET['filtro_stock']) && $_GET['filtro_stock'] !== '') {
            $this->productos = array_filter($this->productos, function($p) {
                if ($_GET['filtro_stock'] === 'bajo') {
                    return $p['stock_inicial'] <= $p['stock_minimo'];
                } elseif ($_GET['filtro_stock'] === 'suficiente') {
                    return $p['stock_inicial'] > $p['stock_minimo'];
                }
                return true;
            });
        }
    }


}

$controlador = new ControladorProductos();
