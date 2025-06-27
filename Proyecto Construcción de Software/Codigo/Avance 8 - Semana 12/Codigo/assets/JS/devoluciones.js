  const selectCliente = document.getElementById('cliente');
  const selectVenta = document.getElementById('venta');
  const selectProducto = document.getElementById('producto');

  selectCliente.addEventListener('change', () => {
    const cliente = selectCliente.value;
    selectVenta.innerHTML = '<option value="">Seleccione una venta</option>';
    selectProducto.innerHTML = '<option value="">Seleccione un producto</option>';
    selectVenta.disabled = true;
    selectProducto.disabled = true;

    if (cliente && estructura[cliente]) {
      const ventas = estructura[cliente].ventas;
      ventas.forEach(venta => {
        const option = document.createElement('option');
        option.value = venta.id;
        option.textContent = `Venta #${venta.id} - ${venta.fecha}`;
        selectVenta.appendChild(option);
      });
      selectVenta.disabled = false;
    }
  });

    selectVenta.addEventListener('change', () => {
    const cliente = selectCliente.value;
    const ventaId = selectVenta.value;
    selectProducto.innerHTML = '<option value="">Seleccione un producto</option>';
    selectProducto.disabled = true;

    if (cliente && ventaId && estructura[cliente]) {
        const venta = estructura[cliente].ventas.find(v => v.id == ventaId);
        if (venta) {
        venta.productos.forEach(producto => {
            const option = document.createElement('option');
            option.value = producto.producto_id;
            option.textContent = producto.nombre_producto;
            selectProducto.appendChild(option);
        });
        selectProducto.disabled = false;
        }
    }
    });

