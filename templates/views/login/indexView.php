<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <img class="d-block mx-auto mb-4" src="<?php echo get_ordify_logo() ?>" alt="<?php echo get_sitename() ?>" width="150">
    <h2>Ingresa a tu cuenta</h2>
    <p class="lead">Administra las compras a tus proveedores fácil y rápido.</p>
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
          <form action="login/post_login" method="post" novalidate>
            <?php echo insert_inputs(); ?>
            
            <div class="row">
              <div class="col-xl-12 mb-3">
                <label for="usuario" >Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Walter White" required>
                <?php if (is_demo()): ?>
                  <span class="text-muted">Ingresa el usuario <b>bee</b></span>
                <?php endif; ?>
              </div>
              <div class="col-xl-12 mb-3">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <?php if (is_demo()): ?>
                  <span class="text-muted">Ingresa la contraseña <b>123456</b></span>
                <?php endif; ?>
              </div>
            </div>

            <button class="btn btn-success btn-block float-end" type="submit">Ingresar</button>

            <div class="mt-3">
              <span class="text-muted"><a href="login/reset">¿Olvidaste tu contraseña?</a></span>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer_v2.php'; ?>

