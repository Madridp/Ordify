  </div> <!-- ends main_wrapper_content -->
  </main>
  </div>
  </div>

  <!-- Globales de configuración Bee -->
  <script>
    var Bee = { 
      csrf: <?php echo sprintf('"%s"', CSRF_TOKEN); ?>,
      redirect_url: <?php echo sprintf('"%s"', CUR_PAGE); ?> ,
      url: <?php echo sprintf('"%s"', URL); ?>,
      sitename: <?php echo sprintf('"%s"', get_sitename()); ?>,
      siteversion: <?php echo sprintf('"%s"', get_version()); ?>
    };
  </script>

  <!-- Font awesome 6 toolkit -->
  <script src="https://kit.fontawesome.com/b110fed35e.js" crossorigin="anonymous"></script>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Select2 -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Feather -->
  <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>

  <!-- toastr js -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- Chartjs -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

  <!-- WaitMe -->
  <script src="<?php echo PLUGINS.'waitme/waitMe.min.js'; ?>"></script>

  <!-- Lightbox2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

  <!-- Main javascript scripts -->
  <script src="<?php echo JS.'main.js?v='.rand(0000,1111); ?>"></script>
</body>
</html>
<!-- ends inc_footer.php -->
