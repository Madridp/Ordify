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

<form action="proveedores/post_agregar" method="post">
  <?php echo insert_inputs(); ?>
  
  <div class="row">
    <div class="col-xl-4 col-lg-6 col-md-8 col-12">
      <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>
      <div class="mb-3">
        <label for="nombre">Nombre *</label>
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Walter White" required>
      </div>
      <div class="mb-3">
        <label for="razon_social">Razón Social *</label>
        <input type="text" class="form-control" id="razon_social" name="razon_social" placeholder="Walter White SA de CV" required>
      </div>
      <div class="mb-3">
        <label for="rfc">RFC *</label>
        <input type="text" class="form-control" id="rfc" name="rfc" placeholder="WWSACV2021123" required>
      </div>
      <div class="mb-3">
        <label for="email">Correo electrónico *</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="walter@white.com" required>
      </div>
      <div class="mb-3">
        <label for="telefono">Teléfono</label>
        <input type="text" class="form-control" id="telefono" name="telefono" placeholder="512345678">
      </div>
      <div class="mb-3">
        <label for="direccion">Dirección *</label>
        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="México Av. Industrial #123" required>
      </div>
      <button class="btn btn-success" type="submit">Guardar</button>
    </div>
  </div>
</form>

<?php require_once INCLUDES.'inc_footer.php'; ?>