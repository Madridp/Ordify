<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="pedidos" class="btn btn-sm btn-outline-success"><i class="fas fa-undo"></i> Regresar</a>
      <a href="<?php echo sprintf('pedidos/export/%s', $d->p->id); ?>" class="btn btn-sm btn-outline-success"><i class="fas fa-file-csv"></i> Exportar</a>
      <a href="<?php echo buildURL(sprintf('pedidos/borrar/%s', $d->p->id)); ?>" class="btn btn-sm btn-outline-danger confirmar"><i class="fas fa-trash"></i> Borrar</a>
    </div>
  </div>
</div>

<div class="wrapper_pedido" data-id="<?php echo $d->p->id; ?>" data-csrf="<?php echo CSRF_TOKEN; ?>">
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
      <!-- selector de productos -->
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
              <div class="mb-3">
                <div class="input-group">
                  <input type="text" class="form-control" id="termino" placeholder="Buscar productos..." aria-label="Buscar productos">
                  <button class="btn btn-outline-secondary do_buscar_productos" type="button"><i class="fas fa-search"></i></button>
                </div>
                <span class="wrapper_msg text-muted"></span>
              </div>
              <form class="do_add_producto_a_pedido" method="post" style="display: none;">
                <?php echo insert_inputs(); ?>
                <input type="hidden" name="id_pedido" value="<?php echo $d->p->id; ?>" required>

                <div class="mb-3 row">
                  <div class="col-xl-10 col-12">
                    <label for="id_producto">Explorar productos *</label>
                    <select name="id_producto" id="id_producto" class="form-select select2-basic">
                      <?php foreach ($d->opciones as $sp): ?>
                        <?php echo sprintf('<option value="%s" data-variante="%s">%s - %s</option>', $sp->id, $sp->variante, $sp->nombre, $sp->corte); ?>
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
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>