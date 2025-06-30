<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Controladores/ControladorDevoluciones.php';
$controlador = new ControladorDevoluciones();

$clientesVentasProductos = $controlador->clientesVentasProductos;
$devoluciones = $controlador->devoluciones;
$mensaje = $controlador->mensaje;
$tipo_mensaje = $controlador->tipo_mensaje;
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Módulo Devolución</title>
  <link rel="stylesheet" href="../assets/css/devoluciones.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<!-- CONTENEDOR PRINCIPAL -->
<div class="conteiner">

  <div class="conteiner-top">
    <h1>REGISTRAR DEVOLUCIÓN</h1>

    <form method="post" id="formDevolucion" class="form-horizontal">
      <input type="hidden" name="accion" value="registrar">


      <div class="grid-form">
        <!-- Etiquetas -->
        <label for="cliente">Cliente</label>
        <label for="venta">Venta</label>
        <label for="producto">Producto</label>

        <!-- Campos -->
        <select id="cliente" name="cliente" required>
          <option value="">Seleccione un cliente</option>
          <?php foreach ($clientesVentasProductos as $cliente => $data): ?>
            <option value="<?= htmlspecialchars($cliente) ?>"><?= htmlspecialchars($cliente) ?></option>
          <?php endforeach; ?>
        </select>

        <select id="venta" name="venta_id" required>
          <option value="">Seleccione una venta</option>
        </select>

        <select id="producto" name="producto_id" required>
          <option value="">Seleccione un producto</option>
        </select>
      </div>

      <div class="grid-form">
        <label for="cantidad">Cantidad</label>
        <label for="fecha">Fecha de Devolución</label>
        <label for="motivo">Motivo de Devolución</label>

        <input type="number" id="cantidad" name="cantidad" min="1" required>
        <input type="date" id="fecha" name="fecha" required>
        <textarea id="motivo" name="motivo" rows="4" required></textarea>
      </div>

      <div class="submit-row">
        <button type="submit" class="btn">Registrar Devolución</button>
      </div>

    </form>

    <?php if (!empty($mensaje)): ?><p class="mensaje" > <strong><?= $mensaje ?></strong> </p> <?php endif; ?>

  </div>

  <div class="conteiner-bottom">
    
    <h1>LISTA DE DEVOLUCIONES</h1>

    <table class="tabla" id="tablaDevoluciones">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Venta</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Motivo</th>
          <th>Fecha</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($devoluciones as $dev): ?>
          <tr>
            <td><?= htmlspecialchars($dev['cliente']) ?></td>
            <td><?= htmlspecialchars($dev['venta_id']) ?></td>
            <td><?= htmlspecialchars($dev['nombre_producto']) ?></td>
            <td><?= htmlspecialchars($dev['cantidad']) ?></td>
            <td><?= htmlspecialchars($dev['motivo']) ?></td>
            <td><?= htmlspecialchars($dev['fecha']) ?></td>
            <td>
              <form method="post" style="display:inline;">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= $dev['id'] ?>">
                <button class="btn btn-eliminar" onclick="return confirm('¿Eliminar esta devolución?')">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>

</div>

<script src="../assets/JS/devoluciones.js"></script>
<script>
       const estructura = <?= json_encode($clientesVentasProductos, JSON_UNESCAPED_UNICODE) ?>;
</script>

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
