<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="proveedores" class="btn btn-sm btn-outline-success"><i class="fas fa-undo"></i> Regresar</a>
    </div>
  </div>
</div>

<form action="pedidos/post_agregar" method="post">
  <?php echo insert_inputs(); ?>
  
  <div class="row">
    <div class="col-xl-4 col-lg-6 col-md-8 col-12">
      <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>

      <?php if (empty($d->proveedores)): ?>
        <div class="alert alert-danger mb-3">
          <p>No hay proveedores registrados en Ordify, por favor agrega uno para continuar.</p>
          <a href="proveedores/agregar" class="btn btn-light">Agregar proveedor</a>
        </div>
      <?php else: ?>
        <div class="mb-3">
          <label for="id_proveedor">Razón Social</label>
          <select class="form-select" name="id_proveedor" id="id_proveedor">
            <?php foreach ($d->proveedores as $prv): ?>
              <?php echo sprintf('<option value="%s">%s</option>', $prv->id, $prv->razon_social) ?>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>
      <div class="mb-3">
        <label for="fecha_entrega">Fecha de entrega estimada *</label>
        <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" required>
      </div>
      <div class="mb-3">
        <label for="notas">Notas</label>
        <textarea class="form-control" id="notas" name="notas"></textarea>
      </div>

      <button class="btn btn-success" type="submit" <?php echo empty($d->proveedores) ? 'disabled' : ''; ?>>Comenzar <i class="fas fa-arrow-right"></i></button>
    </div>
  </div>
</form>

<?php require_once INCLUDES.'inc_footer.php'; ?>