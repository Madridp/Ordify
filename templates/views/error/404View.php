<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container py-5">
  <div class="row">
    <div class="col-6 text-center offset-xl-3">
      <img src="<?php echo get_ordify_logo() ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 200px;">
      <h1 class="mt-5 mb-3"><span class="text-warning">404</span><br>La página no existe</h1>
      <h5 class="text-center">Ejeeeemmm... entraste a otra dimensión, la página que buscas no existe.</h5>
      <div class="mt-5">
        <a class="btn btn-success btn-lg" href="home"><i class="fas fa-undo"></i> Regresar</a>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer_v2.php'; ?>