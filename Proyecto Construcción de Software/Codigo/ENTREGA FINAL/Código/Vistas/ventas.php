<?php
session_start();
if (isset($_SESSION['mensaje_exito'])) {
    echo "<p class='mensaje'>" . htmlspecialchars($_SESSION['mensaje_exito']) . "</p>";
    unset($_SESSION['mensaje_exito']); 
}

require_once __DIR__ . '/../controladores/ControladorVenta.php';

$controlador = new ControladorVenta();
$productos = $controlador->productos ?? [];
$ventas = $controlador->ventas ?? [];
$mensaje = $controlador->mensaje ?? ''; 
$ventaEditar = $controlador->ventaEditar ?? null;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Ventas</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/ventas.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!--CONTENEDOR-->
    <div class="conteiner">

        <div class="conteiner-top">

            <h1>REGISTRAR VENTA</h1>

            <form id="formVenta" method="post" class="form-horizontal">

                <input type="hidden" name="accion" value="<?= $ventaEditar ? 'editar' : 'registrar' ?>">
                    <?php if ($ventaEditar): ?>
                        <input type="hidden" name="id_venta" value="<?= $ventaEditar['id'] ?>">
                    <?php endif; ?>
            
                <div class="grid-form">
                    <!-- FILA DE ETIQUETAS -->
                    <label for="cliente">Cliente</label>
                    <label for="fecha">Fecha de Venta</label>
                    
                    <!-- FILA DE CAMPOS -->
                    <input type="text" id="cliente" name="cliente" required value="<?= $ventaEditar['cliente'] ?? '' ?>">
                    <input type="date" id="fecha" name="fecha" required value="<?= $ventaEditar['fecha'] ?? '' ?>">

                </div>

                <div>
                    <button type="button" class="btn" onclick="agregarFila()">Agregar Producto</button>
                </div>

                <div>
                    <table id="tablaFactura" class="tabla tabla-productos">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total Línea</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Filas dinámicas -->
                        </tbody>
                    </table>

                    <div class="total-general">
                        <label>Total General: </label>
                        <input class="total" type="text" id="totalGeneral" name="totalGeneral"readonly value="0.00">
                    </div>
                </div>

                <input type="hidden" name="accion" value="<?= !empty($ventaEditar) ? 'editar' : 'registrar' ?>">
                <?php if (!empty($ventaEditar)): ?>
                    <input type="hidden" name="id_venta" value="<?= $ventaEditar['id'] ?>">
                <?php endif; ?>

                <div class="submit-row">
                    <button type="submit" class="btn">Registrar Venta</button>
                </div>
            </form>

        <?php if (!empty($mensaje)): ?> <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p> <?php endif; ?>

    </div>

        <div class="conteiner-bottom">

            <h1>LISTA DE VENTAS</h1>

            <table class="tabla" id="tablaVentas">
                <thead>
                    <tr>
                        <th>&nbsp;&nbsp;ID&nbsp;&nbsp;</th>
                        <th>Cliente</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?= htmlspecialchars($venta['id']) ?></td>
                            <td><?= htmlspecialchars($venta['cliente']) ?></td>
                            <td>
                                <button class="btn btn-editar btn-detalles" onclick="mostrarModal(<?= htmlspecialchars(json_encode($venta['detalles'])) ?>)">Ver Detalles</button>
                            </td>
                            <td>$<?= number_format($venta['total'], 2) ?></td>
                            <td><?= htmlspecialchars($venta['fecha']) ?></td>
                            <td>
                                <!-- PARA EDITAR -->
                                <form method="GET" action="" style="display:inline-block;">
                                    <input type="hidden" name="editar_venta_id" value="<?= $venta['id'] ?>">
                                    <button type="submit" class="btn btn-editar">Editar</button>
                                </form>
                                
                                <!-- PARA ELIMINAR -->
                                    <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('¿Eliminar esta venta?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_venta" value="<?= $venta['id'] ?>">
                                        <button type="submit" class="btn btn-eliminar">Eliminar</button>
                                    </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

        </div>

        <!-- MODAL DETALLE DE VENTA -->
        <div id="modalDetalle" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="cerrarModal()">&times;</span>
                <h2>Detalle de Venta</h2>
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total Línea</th>
                        </tr>
                    </thead>
                    <tbody id="detalleBody">
                        <!-- JS insertará filas aquí -->
                    </tbody>
                </table>
                <div class="total-modal">
                    <strong>Total General: $<span id="modalTotalGeneral">0.00</span></strong>
                </div>
            </div>
        </div>

    </div>

<!-- SCRIPT -->
<?php if ($ventaEditar): ?>
    <script>
        window.detallesVentaEditar = <?= json_encode($ventaEditar['detalles']) ?>;
    </script>
<?php endif; ?>

<script src="../assets/JS/ventas.js"></script>

<Script>
    //PRODUCTOS
    const preciosProductos = {};
        <?php foreach ($productos as $producto): ?>
            preciosProductos["<?= htmlspecialchars($producto['nombre']) ?>"] = <?= floatval($producto['precio_venta']) ?>;
        <?php endforeach; ?>
</Script>

<script>
    // Escuchar mensajes del iframe para modo oscuro
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
