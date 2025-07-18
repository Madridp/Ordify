<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
</div>

<div class="row">
  <div class="col-xl-4 col-12">
    <div class="card mb-3">
      <div class="card-header">Actualizaciones del sistema</div>
      <div class="card-body">
        <?php if (version_compare(get_version(), '1.0.0', '>')): ?>
          <a href="<?php echo buildURL('configuracion/update'); ?>" class="btn btn-success confirmar}"><i class="fas fa-database"></i> Actualización disponible</a>        
        <?php else: ?>
          <p class="text-muted m-0">No hay actualizaciones disponibles.</p>
        <?php endif; ?>
      </div>
      <div class="card-footer">
        <?php echo sprintf('Versión %s %s', get_sitename(), get_version()); ?>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-12">
    <div class="card mb-3">
      <div class="card-header">Generar SKUs</div>
      <div class="card-body">
        <a href="<?php echo buildURL('configuracion/generar-skus'); ?>" class="btn btn-success confirmar"><i class="fas fa-database fa-fw"></i> Generar SKUs</a>        
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-12">
    <div class="card mb-3">
      <div class="card-header">Optimizar Imágenes</div>
      <div class="card-body">
        <p>Reduce el tamaño de todas las imágenes de productos para ahorrar espacio en disco reduciendo su peso y tamaño.</p>
        <button class="btn btn-success do_optimizar_imagenes" type="button"><i class="fas fa-image fa-fw"></i> Optimizar ahora</button>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Actualizador de precios</div>
      <div class="card-body">
        <p>Ingresa el precio actual de los productos y el precio nuevo a actualizar.</p>
        <form id="do_update_prices_form">
          <div class="mb-3 row">
            <div class="col-12 col-md-6">
              <label for="precio_actual" class="form-label">Precio actual</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="text" class="form-control" id="precio_actual" name="precio_actual" aria-label="Precio actual" placeholder="0.00">
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label for="precio_nuevo" class="form-label">Nuevo precio</label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="text" class="form-control" id="precio_nuevo" name="precio_nuevo" aria-label="Nuevo precio" placeholder="0.00">
              </div>
            </div>
          </div>

          <button class="btn btn-success" type="submit"><i class="fas fa-wallet fa-fw"></i> Ejecutar ahora</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>