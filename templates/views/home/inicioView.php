<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="row">
  <div class="col-xl-6 col-12">
    <div id="myChartWrapper">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Resumen de pedidos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-3">
            <select name="meses" id="meses" class="form-select">
              <option value="12">Último año</option>
              <option value="6">Último semestre</option>
              <option value="24">24 Meses</option>
              <option value="36">36 Meses</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline-secondary recargar_myChart"><i class="fas fa-redo"></i> Recargar</button>
          </div>
          <!-- <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>
            This week
          </button> -->
        </div>
      </div>

      <canvas class="my-4 w-100" id="chart_pedidos_anual" height="480"></canvas>
    </div>
  </div>
  <div class="col-xl-6 col-12">
    <div id="inversion_anual">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Resumen de inversión</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary recargar_chart_inversion_anual"><i class="fas fa-redo"></i> Recargar</button>
          </div>
        </div>
      </div>

      <canvas class="my-4 w-100" id="chart_inversion_anual" height="480"></canvas>
    </div>
  </div>
</div>


<?php require_once INCLUDES.'inc_footer.php'; ?>