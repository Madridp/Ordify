<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <img class="d-block mx-auto mb-4" src="<?php echo get_ordify_logo(); ?>" alt="<?php echo get_sitename() ?>" width="150">
    <h2><?php echo $d->title; ?></h2>
    <p class="lead">Te enviaremos un correo electrónico para reiniciar tu contraseña.</p>
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
          <form action="login/post_reset" method="post" novalidate>
            <?php echo insert_inputs(); ?>
            
            <div class="row">
              <div class="col-xl-12 mb-3">
                <label for="email" >Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="walter@white.com" required>
              </div>
            </div>

            <button class="btn btn-success btn-block float-end" type="submit">Ingresar</button>

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

