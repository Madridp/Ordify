<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="productos" class="btn btn-sm btn-outline-success"><i class="fas fa-undo"></i> Regresar</a>
    </div>
  </div>
</div>

<form action="productos/post_agregar" method="post" enctype="multipart/form-data">
  <?php echo insert_inputs(); ?>
  
  <div class="row">
    <div class="col-xl-4 col-lg-6 col-md-8 col-12">
      <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>

      <div class="mb-3">
        <label for="imagen" class="form-label">Imagen del producto</label>
        <input class="form-control" type="file" id="imagen" name="imagen" accept="image/x-png,image/gif,image/jpeg">
      </div>
      
      <div class="mb-3">
        <label for="nombre">Nombre del producto *</label>
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Playera de cuello redondo" required>
      </div>

      <div class="mb-3">
        <label for="atributo">Atributo de producto *</label>
        <select class="form-select" name="atributo" id="atributo" required>
          <option value="none" selected>Selecciona una opción...</option>
          <?php foreach ($d->variantes as $v): ?>
            <?php echo sprintf('<option value="%s">%s</option>', $v->titulo, $v->titulo); ?>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="variantes">Variantes del producto *</label>
        <input type="text" class="form-control" id="variantes" name="variantes" placeholder="S|M|L|XL" value="S|M|L|XL" required>
        <span class="text-muted">Separa con <span class="text-danger">|</span> cada variante del producto.</span>
      </div>

      <div class="mb-3">
        <label for="precio">Precio *</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input type="text" class="form-control" name="precio" id="precio" placeholder="199.99" aria-label="Precio" required>
        </div>
      </div>

      <div class="mb-3">
        <label for="descripcion">Descripción</label>
        <textarea class="form-control" name="descripcion" id="descripcion" cols="6" rows="6"></textarea>
      </div>

      <div class="mb-3">
        <label for="sku">SKU</label>
        <input type="text" class="form-control" id="sku" name="sku" placeholder="64AD-123">
      </div>

      <div class="mb-3">
        <label for="adjuntos">Link de adjuntos externos</label>
        <input type="text" class="form-control" id="adjuntos" name="adjuntos">
      </div>

      <button class="btn btn-success" type="submit">Guardar</button>
    </div>
  </div>
</form>

<?php require_once INCLUDES.'inc_footer.php'; ?>