<?php
require_once __DIR__ . '/../Controladores/ControladorProductos.php';
$controlador = new ControladorProductos();
$productos = $controlador->productos;
$mensaje = $controlador->mensaje;
$productoEditar = $controlador->productoEditar;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Productos</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/productos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!--CONTENEDOR-->
    <div class="conteiner">

        <div class="conteiner-top">

            <h1>REGISTRAR PRODUCTOS</h1>
    
            <form method="POST" id="formProducto" class="form-horizontal">
            <input type="hidden" name="accion" value="<?= $productoEditar ? 'editar' : 'registrar' ?>">

                    <div class="grid-form">
                        <!-- FILA DE ETIQUETAS -->
                        <label for="codigo">Código</label>
                        <label for="nombre">Nombre</label>
                        <label for="descripcion">Descripción</label>
                        <label for="categoria">Categoría</label>
            
                        <!-- FILA DE CAMPOS -->
                        <input type="text" id="codigo" name="codigo" required value="<?= $productoEditar['codigo'] ?? '' ?>" <?= $productoEditar ? 'readonly' : '' ?>>
                        <input type="text" id="nombre" name="nombre" required value="<?= $productoEditar['nombre'] ?? '' ?>">
                        <input type="text" id="descripcion" name="descripcion" required value="<?= $productoEditar['descripcion'] ?? '' ?>">
                        <select id="categoria" name="categoria">
                            <option value="electrónica" <?= ($productoEditar['categoria'] ?? '') === 'electrónica' ? 'selected' : '' ?>>Electrónica</option>
                            <option value="ropa" <?= ($productoEditar['categoria'] ?? '') === 'ropa' ? 'selected' : '' ?>>Ropa</option>
                            <option value="alimentos" <?= ($productoEditar['categoria'] ?? '') === 'alimentos' ? 'selected' : '' ?>>Alimentos</option>
                            <option value="muebles" <?= ($productoEditar['categoria'] ?? '') === 'muebles' ? 'selected' : '' ?>>Muebles</option>
                            <option value="papeleria" <?= ($productoEditar['categoria'] ?? '') === 'papeleria' ? 'selected' : '' ?>>Papelería</option>
                        </select>

                    </div>
            
                    <div class="grid-form">
                        <label for="precio_venta">Precio Venta</label>
                        <label for="precio_compra">Precio Compra</label>
                        <label for="stock_inicial">Stock Inicial</label>
                        <label for="stock_minimo">Stock Mínimo</label>
                        
            
                        <input type="number" id="precio_venta" name="precio_venta" step="0.01" required value="<?= $productoEditar['precio_venta'] ?? '' ?>">
                        <input type="number" id="precio_compra" name="precio_compra" step="0.01" required value="<?= $productoEditar['precio_compra'] ?? '' ?>">
                        <input type="number" id="stock_inicial" name="stock_inicial" required value="<?= $productoEditar['stock_inicial'] ?? '' ?>">
                        <input type="number" id="stock_minimo" name="stock_minimo" required value="<?= $productoEditar['stock_minimo'] ?? '' ?>">
                        
                    </div>
            
                    <div class="submit-row">
                        <button type="submit" class="btn"><?= $productoEditar ? 'Actualizar' : 'Registrar' ?></button>
                    </div>
            </form>
                    
                <?php if (!empty($mensaje)): ?><p class="mensaje" > <strong><?= $mensaje ?></strong> </p> <?php endif; ?>


        </div>

        <div class="conteiner-bottom">

            <!-- FORMULARIO DE FILTROS -->

            <form method="GET" class="form-horizontal form-horizontal-consul">
                <div class="grid-form grid-form-consul">
                    <label for="filtro_codigo">Filtrar por Codigo:</label>
                    <label for="filtro_categoria">Filtrar por Categoría:</label>
                    <label for="filtro_stock">Filtrar por Stock:</label>

                    <input type="text" id="filtro_codigo" name="filtro_codigo" value="<?= isset($_GET['filtro_codigo']) ? htmlspecialchars($_GET['filtro_codigo']) : '' ?>">
                    <select id="filtro_categoria" name="filtro_categoria">
                        <option value="">Todas</option>
                        <option value="electrónica" <?= (isset($_GET['filtro_categoria']) && $_GET['filtro_categoria'] == 'electrónica') ? 'selected' : '' ?>>Electrónica</option>
                        <option value="ropa" <?= (isset($_GET['filtro_categoria']) && $_GET['filtro_categoria'] == 'ropa') ? 'selected' : '' ?>>Ropa</option>
                        <option value="alimentos" <?= (isset($_GET['filtro_categoria']) && $_GET['filtro_categoria'] == 'alimentos') ? 'selected' : '' ?>>Alimentos</option>
                        <option value="muebles" <?= (isset($_GET['filtro_categoria']) && $_GET['filtro_categoria'] == 'muebles') ? 'selected' : '' ?>>Muebles</option>
                        <option value="papeleria" <?= (isset($_GET['filtro_categoria']) && $_GET['filtro_categoria'] == 'papeleria') ? 'selected' : '' ?>>Papelería</option>
                    </select>
                    <select id="filtro_stock" name="filtro_stock">
                        <option value="">Todos</option>
                        <option value="bajo" <?= (isset($_GET['filtro_stock']) && $_GET['filtro_stock'] == 'bajo') ? 'selected' : '' ?>>Stock Bajo</option>
                        <option value="suficiente" <?= (isset($_GET['filtro_stock']) && $_GET['filtro_stock'] == 'suficiente') ? 'selected' : '' ?>>Stock Suficiente</option>
                    </select>
                </div>
                <div class="submit-row">
                    <button type="submit" class="btn btn-consul">Filtrar</button>
                </div>
            </form>

        
            <table class="tabla" id="tablaProductos">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio de Venta</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                        <tr>
                            <td><?= $p['codigo'] ?></td>
                            <td><?= $p['nombre'] ?></td>
                            <td><?= $p['categoria'] ?></td>
                            <td><?= $p['precio_venta'] ?></td>
                            <td><?= $p['stock_inicial'] ?></td>
                            <td>
                                <!-- PARA EDITAR -->
                                <form method="GET" action="" style="display:inline-block">
                                    <input type="hidden" name="editar_codigo" value="<?= htmlspecialchars($p['codigo']) ?>">
                                    <button type="submit" class="btn btn-editar">Editar</button>
                                </form>
                                <!-- PARA ELIMINAR -->
                                <form method="POST" style="display:inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="codigo" value="<?= htmlspecialchars($p['codigo']) ?>">
                                    <button type="submit" class="btn btn-eliminar">Eliminar</button>
                                </form>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
            
        </div>

    </div>

<!-- SCRIPT -->
<script>
// Escuchar mensajes del iframe
window.addEventListener('message', function(event) {
      if (event.data.darkMode !== undefined) {
          if (event.data.darkMode) {
              document.body.classList.add('dark');
          } else {
              document.body.classList.remove('dark');
          }
      }
  });

  // Pedir el estado actual cuando cargue
  window.parent.postMessage({ reloadStyles: true }, '*');
</script>
</body>
</html>