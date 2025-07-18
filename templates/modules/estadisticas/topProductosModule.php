<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2">Productos más solicitados</h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <button type="button" class="btn btn-sm btn-outline-secondary recargar_top_products"><i class="fas fa-redo"></i> Recargar</button>
    </div>
  </div>
</div>

<?php if (!empty($d)): ?>
  <ul class="list-group">
    <li class="list-group-item d-flex justify-content-between align-items-start">
      <div class="me-4">
        <div class="fw-bold">#</div>
      </div>
      <div class="me-auto">
        <div class="fw-bold">Información</div>
      </div>
      <div>
        <span class="fw-bold">Solicitados</span>
      </div>
    </li>
    <?php $i = 1; ?>
    <?php foreach ($d as $producto): ?>
      <li class="list-group-item d-flex justify-content-between align-items-start">
        <div class="me-3 mt-2" style="width: 10px;">
          <span class="badge bg-success rounded-pill">
            <?php echo $i; ?>
          </span>
          <?php $i++; ?>
        </div>
        <div class="ms-2">
          <?php if (is_file(UPLOADS.$producto->imagen)): ?>
            <img src="<?php echo sprintf('%s%s', UPLOADED, $producto->imagen); ?>" alt="<?php echo $producto->pp_producto; ?>" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;">
          <?php else: ?>
            <img src="<?php echo sprintf('%s%s', IMAGES, 'noimage.jpg'); ?>" alt="<?php echo $producto->pp_producto; ?>" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover;">
          <?php endif; ?>
        </div>
        <div class="ms-2 me-auto">
          <div class="fw-bold"><?php echo $producto->pp_producto; ?></div>
          <span class="text-muted"><?php echo sprintf('%s', $producto->corte); ?></span>
        </div>
        <span class="badge bg-primary rounded-pill"><?php echo $producto->total; ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <h5 class="text-muted">No hay productos para mostrar.</h5>
  <p>Actualmente no contamos con información suficiente para mostrar los productos más solicitados.</p>
<?php endif; ?>