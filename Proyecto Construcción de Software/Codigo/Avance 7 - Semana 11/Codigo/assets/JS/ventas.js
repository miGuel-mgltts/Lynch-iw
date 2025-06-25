
function agregarFila() {
  const tbody = document.getElementById('tablaFactura').getElementsByTagName('tbody')[0];

  const fila = tbody.insertRow();

  // Celda producto
  const celdaProducto = fila.insertCell(0);
  const select = document.createElement('select');
  select.name = "productos[]";
  select.className = "campo";

  const opcionInicial = document.createElement('option');
  opcionInicial.value = "";
  opcionInicial.text = "Seleccionar";
  opcionInicial.disabled = true;
  opcionInicial.selected = true;
  select.appendChild(opcionInicial);

  for (const producto in preciosProductos) {
    const option = document.createElement('option');
    option.value = producto;
    option.text = producto;
    select.appendChild(option);
  }
  select.onchange = () => actualizarPrecioYTotal(fila);
  celdaProducto.appendChild(select);

  // Celda cantidad
  const celdaCantidad = fila.insertCell(1);
  const inputCantidad = document.createElement('input');
  inputCantidad.type = "number";
  inputCantidad.name = "cantidades[]";
  inputCantidad.min = "1";
  inputCantidad.value = "1";
  inputCantidad.className = "campo";
  inputCantidad.oninput = () => actualizarPrecioYTotal(fila);
  celdaCantidad.appendChild(inputCantidad);

  // Celda precio
  const celdaPrecio = fila.insertCell(2);
  const inputPrecio = document.createElement('input');
  inputPrecio.type = "text";
  inputPrecio.name = "precios[]";
  inputPrecio.readOnly = true;
  inputPrecio.value = "";
  inputPrecio.className = "campo";
  celdaPrecio.appendChild(inputPrecio);

  // Celda total línea
  const celdaTotal = fila.insertCell(3);
  const inputTotal = document.createElement('input');
  inputTotal.type = "text";
  inputTotal.name = "totales[]";
  inputTotal.readOnly = true;
  inputTotal.value = "";
  inputTotal.className = "campo";
  celdaTotal.appendChild(inputTotal);

  // Celda acciones
  const celdaAcciones = fila.insertCell(4);
  const botonEliminar = document.createElement('button');
  botonEliminar.textContent = "Eliminar";
  botonEliminar.className = "btn btn-eliminar";
  botonEliminar.onclick = () => {
    fila.remove();
    actualizarTotalGeneral();
  };
  celdaAcciones.appendChild(botonEliminar);
}

function actualizarPrecioYTotal(fila) {
  const select = fila.cells[0].querySelector('select');
  const cantidadInput = fila.cells[1].querySelector('input');
  const precioInput = fila.cells[2].querySelector('input');
  const totalInput = fila.cells[3].querySelector('input');

  if (select.value === "") {
    precioInput.value = "";
    totalInput.value = "";
    actualizarTotalGeneral();
    return;
  }

  const precio = preciosProductos[select.value];
  precioInput.value = precio.toFixed(2);

  const cantidad = parseFloat(cantidadInput.value);
  const total = precio * cantidad;
  totalInput.value = total.toFixed(2);

  actualizarTotalGeneral();
}

function actualizarTotalGeneral() {
  const filas = document.querySelectorAll('#tablaFactura tbody tr'); //Selecciona todas la filas <tr> dentro del <tbody> de la tabla con el id "tablaFactura"
  let totalGeneral = 0;

  filas.forEach(fila => {
    const totalLinea = parseFloat(fila.cells[3].querySelector('input').value) || 0;
    totalGeneral += totalLinea;
  });

  document.getElementById('totalGeneral').value = totalGeneral.toFixed(2);
}
