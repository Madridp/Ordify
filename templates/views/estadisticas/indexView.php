<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="row">
  <div class="col-xl-6 col-12">
    <div class="mb-3" id="wrapper_chart_top_products">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Top 10 Productos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary recargar_chart_top_products"><i class="fas fa-redo"></i> Recargar</button>
          </div>
        </div>
      </div>

      <canvas class="my-4 w-100" id="chart_top_products" height="560"></canvas>
    </div>
  </div>

  <div class="col-xl-6 col-12">
    <div id="wrapper_top_products">
      <!-- topProductosModule.php -->
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>