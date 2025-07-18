<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="productos" class="btn btn-sm btn-outline-success"><i class="fas fa-undo"></i> Regresar</a>
      <a href="<?php echo sprintf('productos/editar/%s', $d->p->id); ?>" class="btn btn-sm btn-outline-success"><i class="fas fa-edit"></i> Editar</a>
      <a href="<?php echo buildURL(sprintf('productos/borrar/%s', $d->p->id)); ?>" class="btn btn-sm btn-outline-danger confirmar"><i class="fas fa-trash"></i> Borrar</a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-4 col-lg-6 col-md-8 col-12">
    <div class="mb-3">
      <label for="imagen" class="form-label">Imagen del producto</label>
        <div class="d-block mb-2">
          <?php if (is_file(UPLOADS.$d->p->imagen)): ?>
            <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">', ASSETS.'uploads/'.$d->p->imagen, $d->p->nombre); ?>
          <?php else: ?>
            <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">', IMAGES.'noimage.jpg', $d->p->nombre); ?>
          <?php endif; ?>
        </div>
    </div>
    
    <div class="mb-3">
      <label for="nombre">Nombre del producto *</label>
      <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $d->p->nombre; ?>" disabled>
    </div>

    <div class="mb-3">
      <label for="corte">Corte del producto *</label>
      <input type="text" class="form-control" id="corte" name="corte" placeholder="S|M|L|XL" value="<?php echo $d->p->corte; ?>" disabled>
    </div>

    <div class="mb-3">
      <label for="variantes">Variantes del producto *</label>
      <input type="text" class="form-control" id="variantes" name="variantes" placeholder="S|M|L|XL" value="<?php echo $d->p->variantes; ?>" disabled>
    </div>

    <div class="mb-3">
      <label for="precio">Precio *</label>
      <div class="input-group">
        <span class="input-group-text">$</span>
        <input type="text" class="form-control" name="precio" id="precio" placeholder="199.99" aria-label="Precio" value="<?php echo $d->p->precio; ?>" disabled>
      </div>
    </div>

    <div class="mb-3">
      <label for="descripcion">Descripción</label>
      <textarea class="form-control" name="descripcion" id="descripcion" cols="6" rows="6" disabled><?php echo $d->p->descripcion; ?></textarea>
    </div>

    <div class="mb-3">
      <label for="sku">SKU</label>
      <input type="text" class="form-control" id="sku" name="sku" placeholder="64AD-123" value="<?php echo $d->p->sku; ?>" disabled>
    </div>

    <div class="mb-3">
      <label for="adjuntos">Link de adjuntos externos</label>
      <?php if (!empty($d->p->adjuntos) && filter_var($d->p->adjuntos, FILTER_VALIDATE_URL)): ?>
        <span class="text-primary d-block"><a href="<?php echo $d->p->adjuntos; ?>" target="_blank"><i class="fas fa-link"></i> <?php echo $d->p->adjuntos; ?></a></span>
      <?php else: ?>
        <input type="text" class="form-control" id="adjuntos" name="adjuntos" value="<?php echo $d->p->adjuntos; ?>" disabled>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>