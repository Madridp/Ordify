<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
</div>

<div class="row">
  <div class="col-xl-4 col-12">
    <div class="card">
      <div class="card-header">Nuevo Usuario</div>
      <div class="card-body">
        <form action="usuarios/post_agregar" method="post" class="mb-3">
          <?php echo insert_inputs(); ?>

          <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>
          <div class="mb-3">
            <label for="nombre">Nombre *</label>
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Walter White" required>
          </div>
          <div class="mb-3 row">
            <div class="col-12 col-md-6">
              <label for="usuario">Usuario *</label>
              <input type="text" class="form-control" id="usuario" name="usuario" placeholder="walterwhite" required>
            </div>
            <div class="col-12 col-md-6">
              <label for="role">Role de usuario *</label>
              <select name="role" id="role" class="form-select">
                <?php foreach (get_roles() as $role): ?>
                  <?php echo sprintf('<option value="%s">%s</option>', $role[0], $role[1]); ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label for="email">Correo electrónico *</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="walter@white.com" required>
          </div>
          <div class="mb-3">
            <label for="password">Contraseña *</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="mb-3">
            <label for="password_conf">Confirmar contraseña *</label>
            <input type="password" class="form-control" id="password_conf" name="password_conf" required>
          </div>

          <button class="btn btn-success" type="submit">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>