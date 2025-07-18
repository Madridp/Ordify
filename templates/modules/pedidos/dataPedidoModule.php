<h5 class="mb-4">Resumen de pedido <span class="float-end"><?php echo format_estado_pedido($d->status); ?></span></h5>

<div class="card shadow mb-3">
  <div class="card-header">Contenido del pedido</div>
  <div class="card-body">
    <!-- Contenido del pedido -->
    <?php if ($d->total_cantidades == 0): ?>
      <p>Este pedido aún <span class="badge bg-danger">no tiene contenido</span></p>
    <?php elseif ($d->total_cantidades == 1): ?>
      <p>Este pedido tiene <span class="badge bg-warning text-dark">una sola unidad</span> agregada.</p>
    <?php elseif ($d->total_cantidades > 1): ?>
      <p><?php echo sprintf('Este pedido tiene <span class="badge bg-success">%s unidades</span> agregadas.', $d->total_cantidades); ?></p>
    <?php endif; ?>
    
    <!-- Unidades o cantidades procesadas -->
    <?php if ($d->status === 'completado'): ?>
      <p><?php echo sprintf('Este pedido incluye <b>%s</b> piezas totales de <b>%s</b> %s, <b>%s</b> procesadas.', 
      $d->total_cantidades, $d->total_productos, $d->total_cantidades === 1 ? 'producto' : 'productos', $d->total_procesados); ?></p>
      
      <?php if ($d->total_rechazados > 0): ?>
        <!-- Unidades dañadas -->
        <p>
          <?php echo sprintf('Se recibieron <b>%s</b> %s.', 
            $d->total_rechazados,
            $d->total_rechazados == 1 ? 'unidad dañada' : 'unidades dañadas'
          ); ?>
        </p>
      <?php endif; ?>
    
      <?php if ($d->total_cancelados > 0): ?>
        <!-- Unidades dañadas -->
        <p>
          <?php echo sprintf('Se recibieron <b>%s</b> %s.', 
            $d->total_cancelados,
            $d->total_cancelados == 1 ? 'unidad cancelada' : 'unidades canceladas'
          ); ?>
        </p>
      <?php endif; ?>
    <?php endif; ?>
    
    <!-- Datos del pedido en caso de estar siendo procesado -->
    <?php if (in_array($d->status, ['procesando'])): ?>
      <?php if ($d->total_procesados == 0): ?>
        <p><span class="badge bg-warning text-dark">Empezaremos a procesar las unidades del pedido en breve.</span></p>
      <?php elseif ((int) $d->total_procesados === 1): ?>
        <p>Hemos procesado <span class="badge bg-info">1 unidad</span> hasta el momento con éxito.</p>
      <?php elseif ($d->total_procesados > 1): ?>
        <p><?php echo sprintf('Hemos procesado <span class="badge bg-info text-dark">%s de %s unidades</span> con éxito.', $d->total_procesados, $d->total_cantidades); ?></p>
      <?php endif; ?>
    <?php endif; ?>
    
    <!-- Envío y fecha de recepción -->
    <?php if (in_array($d->status, ['borrador','pendiente','en_camino'])): ?>
      <p><?php echo sprintf('<i class="fas fa-clock"></i> Se estima que llegará el <b>%s</b>.', format_date($d->fecha_entrega)); ?></p>
    <?php elseif (in_array($d->status, ['recibido','procesando','completado'])): ?>
      <p><?php echo sprintf('<i class="fas fa-box"></i> Llegó el <b>%s</b>.', format_date($d->fecha_entrega)); ?></p>
    <?php endif; ?>
  </div>
</div>

<div class="mb-3">
  <?php echo sprintf('<a class="btn btn-warning d-block" href="%s" target="_blank"><i class="fas fa-link"></i> Compartir enlace público</a>', format_pedido_url($d->hash)); ?>
</div>