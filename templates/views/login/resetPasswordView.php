<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <img class="d-block mx-auto mb-4" src="<?php echo get_ordify_logo(); ?>" alt="<?php echo get_sitename() ?>" width="150">
    <h2><?php echo $d->title; ?></h2>
    <p class="lead">Ingresa tu nueva contraseña.</p>
  </div>

  <div class="row">
    <!-- formulario -->
    <div class="offset-xl-3 col-xl-6">
      <div class="row">
        <div class="col-12">
          <?php echo Flasher::flash(); ?>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Completa el formulario</h4>
        </div>
        <div class="card-body">
          <form action="login/post_reset_password" method="post">
            <?php echo insert_inputs(); ?>
            <input type="hidden" name="id" value="<?php echo $d->p->id_usuario; ?>" required>
            <input type="hidden" name="token" value="<?php echo $d->p->contenido; ?>" required>
            
            <div class="row">
              <div class="col-xl-12 mb-3">
                <label for="password" >Nueva contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>

              <div class="col-xl-12 mb-3">
                <label for="conf_password" >Confirma tu contraseña</label>
                <input type="password" class="form-control" id="conf_password" name="conf_password" required>
              </div>
            </div>

            <button class="btn btn-success btn-block float-end" type="submit">Guardar cambios</button>

            <div class="mt-3">
              <span class="text-muted">¿Ya tienes cuenta? <a href="login">Ingresar</a></span>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer_v2.php'; ?>

