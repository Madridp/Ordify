<div class="row">
  <!-- información general del pedido -->
  <div class="col-xl-4 col-lg-6 col-md-8 col-12">
    <div class="wrapper_data_pedido">
      <!-- dataPedidoModule.php -->
    </div>

    <!-- información del proveedor -->
    <div class="wrapper_data_proveedor">
      <!-- dataProveedorModule.php -->
    </div>
  </div>

  <!-- tabla de productos en el pedido -->
  <div class="col-xl-8 col-lg-6 col-md-4 col-12">
    <?php if (in_array($d->p->status, ['borrador'])): ?>
      <div class="card mb-3">
        <div class="card-header">Agregar más productos</div>
        <div class="card-body">
          <?php if (empty($d->opciones)): ?>
            <div class="alert alert-danger">
              <p>No hay productos en la base de datos, agrega por lo menos uno para continuar.</p>
              <a href="productos/agregar" class="btn btn-light btn-sm text-danger">Agregar producto</a>
            </div>
          <?php else: ?>
            <form class="do_add_producto_a_pedido" method="post">
              <?php echo insert_inputs(); ?>
              <input type="hidden" name="id_pedido" value="<?php echo $d->id; ?>" required>

              <div class="mb-3 row">
                <div class="col-xl-10 col-12">
                  <label for="id_producto">Explorar productos *</label>
                  <select name="id_producto" id="id_producto" class="form-select select2-basic">
                    <?php foreach ($d->opciones as $sp): ?>
                      <?php echo sprintf('<option value="%s" data-variante="%s">%s -  %s</option>', $sp->id, $sp->variante, $sp->nombre, $sp->corte); ?>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-xl-2 col-12">
                  <label for="" class="d-block">Acción</label>
                  <button class="btn btn-success btn-block" type="submit"><i class="fas fa-plus"></i> Agregar</button>
                </div>
              </div>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="wrapper_data_productos">
      <!-- dataProductosModule.php -->
    </div>
  </div>
</div>