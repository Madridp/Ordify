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

<form action="proveedores/post_editar" method="post">
  <?php echo insert_inputs(); ?>
  <input type="hidden" name="id" value="<?php echo $d->p->id; ?>" required>
  
  <div class="row">
    <div class="col-xl-4 col-lg-6 col-md-8 col-12">
      <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>
      <div class="mb-3">
        <label for="nombre">Nombre *</label>
        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $d->p->nombre; ?>" required>
      </div>
      <div class="mb-3">
        <label for="razon_social">Razón Social *</label>
        <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $d->p->razon_social; ?>" required>
      </div>
      <div class="mb-3">
        <label for="rfc">RFC *</label>
        <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $d->p->rfc; ?>" required>
      </div>
      <div class="mb-3">
        <label for="email">Correo electrónico *</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $d->p->email; ?>" required>
      </div>
      <div class="mb-3">
        <label for="telefono">Teléfono</label>
        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $d->p->telefono; ?>">
      </div>
      <div class="mb-3">
        <label for="direccion">Dirección *</label>
        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $d->p->direccion; ?>" required>
      </div>
      <button class="btn btn-success" type="submit">Guardar</button>
    </div>
  </div>
</form>

<?php require_once INCLUDES.'inc_footer.php'; ?>