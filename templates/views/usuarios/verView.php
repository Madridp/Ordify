<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="usuarios" class="btn btn-sm btn-outline-success"><i class="fas fa-undo"></i> Regresar</a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-4 col-12">
    <div class="card">
      <div class="card-header"><?php echo $d->title; ?></div>
      <div class="card-body">
        <form action="usuarios/post_editar" method="post" class="mb-3">
          <?php echo insert_inputs(); ?>
          <input type="hidden" name="id" value="<?php echo $d->u->id; ?>" required>

          <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>
          <div class="mb-3">
            <label for="nombre">Nombre *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $d->u->nombre; ?>" placeholder="Walter White" required>
          </div>
          <div class="mb-3 row">
            <div class="col-12 col-md-6">
              <label for="usuario">Usuario *</label>
              <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo $d->u->usuario; ?>" placeholder="walterwhite" required>
            </div>
            <div class="col-12 col-md-6">
              <label for="role">Role de usuario *</label>
              <select name="role" id="role" class="form-select">
                <?php foreach (get_roles() as $role): ?>
                  <?php echo sprintf('<option value="%s" %s>%s</option>', 
                  $role[0], 
                  $role[0] === $d->u->role ? 'selected' : null, 
                  $role[1]); 
                ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label for="email">Correo electrónico *</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $d->u->email; ?>" placeholder="walter@white.com" required>
          </div>
          <div class="mb-3">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password">
          </div>
          <div class="mb-3">
            <label for="password_conf">Confirmar contraseña</label>
            <input type="password" class="form-control" id="password_conf" name="password_conf">
          </div>

          <button class="btn btn-success" type="submit">Guardar cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>