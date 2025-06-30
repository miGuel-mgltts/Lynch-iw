<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo Proveedores</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/provedores.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php


// Inicia la sesión 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../Controladores/controladorProveedor.php'; 


$controlador = new ControladorProveedor();


$proveedores = $controlador->proveedores;
$productos = $controlador->productos;
$mensaje = $controlador->mensaje;
$tipo_mensaje = $controlador->tipo_mensaje; 
$proveedorEditar = $controlador->proveedorEditar; 
?>

<div class="conteiner">

    <div class="conteiner-top">
        <h1>REGISTRAR PROVEEDOR</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="mensaje <?php echo htmlspecialchars($tipo_mensaje); ?>">
                <strong><?php echo htmlspecialchars($mensaje); ?></strong>
            </div>
        <?php endif; ?>

        <form method="post" id="formProveedor" class="form-horizontal" action="/Vistas/proveedores.php"> 
            <input type="hidden" name="accion" value="<?= $proveedorEditar ? 'actualizar' : 'registrar' ?>">
            <input type="hidden" name="ruc_original" value="<?= $proveedorEditar['ruc'] ?? '' ?>">

            <div class="grid-form">
                <label for="cedula">Cédula</label>
                <label for="nombre">Nombre</label>
                <label for="ruc">RUC</label>
                <label for="telefono">Teléfono</label>

                <input type="text" id="cedula" name="cedula" value="<?= htmlspecialchars($proveedorEditar['cedula'] ?? '') ?>">
                <input type="text" id="nombre" name="nombre" required value="<?= htmlspecialchars($proveedorEditar['nombre'] ?? '') ?>">
                <input type="text" id="ruc" name="ruc" required value="<?= htmlspecialchars($proveedorEditar['ruc'] ?? '') ?>" <?= $proveedorEditar ? 'readonly' : '' ?>>
                <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($proveedorEditar['telefono'] ?? '') ?>">
            </div>

            <div class="grid-form">
                <label for="direccion">Dirección</label>
                <label for="correo">Correo</label>
                <label for="id_producto">Producto Asociado</label>
                <label></label>

                <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($proveedorEditar['direccion'] ?? '') ?>">
                <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($proveedorEditar['correo'] ?? '') ?>">
                <select id="id_producto" name="id_producto" required>
                    <option value="">Seleccione un producto</option>
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?php echo htmlspecialchars($prod['id']); ?>"
                                <?= ($proveedorEditar && $proveedorEditar['id_producto'] == $prod['id']) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($prod['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div></div>
            </div>

            <div class="submit-row">
                <button type="submit" class="btn"><?= $proveedorEditar ? 'Actualizar' : 'Registrar' ?></button>
                <?php if ($proveedorEditar): ?>
                    <button type="button" class="btn btn-reset" onclick="window.location.href='/Vistas/proveedores.php'">Cancelar Edición</button>
                <?php endif; ?>
            </div>
        </form>

    </div>

    <div class="conteiner-bottom">
        <h1>LISTA DE PROVEEDORES</h1>

        <table class="tabla">
            <thead>
                <tr>
                    <th>Cédula</th>
                    <th>RUC</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Correo</th>
                    <th>Producto Asociado</th> 
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($proveedores)): ?>
                    <?php foreach ($proveedores as $prov): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prov['cedula'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($prov['ruc']); ?></td>
                            <td><?php echo htmlspecialchars($prov['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($prov['telefono'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($prov['direccion'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($prov['correo'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($prov['nombre_producto'] ?? 'N/A'); ?></td> 
                            <td>
                                <form method="GET" action="/Vistas/proveedores.php" style="display:inline-block">
                                    <input type="hidden" name="editar_ruc" value="<?= htmlspecialchars($prov['ruc']) ?>">
                                    <button type="submit" class="btn btn-editar">Editar</button>
                                </form>
                                <form method="POST" action="/Vistas/proveedores.php" style="display:inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este proveedor? (Se deshabilitará)')">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="ruc" value="<?= htmlspecialchars($prov['ruc']) ?>">
                                    <button type="submit" class="btn btn-eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No hay proveedores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    
    window.addEventListener('message', function(event) {
        if (event.data.darkMode !== undefined) {
            if (event.data.darkMode) {
                document.body.classList.add('dark');
            } else {
                document.body.classList.remove('dark');
            }
        }
    });

    
    window.parent.postMessage({ reloadStyles: true }, '*');
</script>
</body>
</html>