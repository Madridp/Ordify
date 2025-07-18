<?php require_once INCLUDES.'inc_header.php'; ?>

<div class="container pt-2 pb-5">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo $d->title; ?></h1>
    <span class="float-end"><img src="<?php echo get_ordify_logo(); ?>" alt="<?php echo get_sitename(); ?>" style="width: 150px;"></span>
  </div>

  <div class="row">
    <!-- alert -->
    <?php if (Auth::validate()): ?>
      <div class="alert alert-danger">
        <p class="text-truncate">Estás viendo la versión pública del pedido <b>#<?php echo $d->p->numero; ?></b>, así lo verá tu proveedor, el link de acceso será enviado por correo electrónico al <b>"realizar"</b> el pedido o compartiendo este enlace <b><?php echo $d->url; ?></b></p>
        <p>Para ver el pedido en tu panel de administración ve <?php echo sprintf('<a href="pedidos/ver/%s">aquí</a>.', $d->p->id); ?></p>
      </div>
    <?php endif; ?>

    <!-- información general del pedido -->
    <div class="col-xl-4 col-lg-6 col-md-12 col-12">
      <h5 class="mb-4">Resumen de pedido <span class="float-end"><?php echo format_estado_pedido($d->p->status); ?></span></h5>

      <div class="card shadow mb-3">
        <div class="card-header">Contenido del pedido</div>
        <div class="card-body">
          <p><?php echo sprintf('Este pedido incluye <b>%s</b> piezas totales de <b>%s</b> %s, <b>%s</b> procesadas hasta el momento.', 
          $d->p->total_cantidades, $d->p->total_productos, $d->p->total_cantidades === 1 ? 'producto' : 'productos', $d->p->total_procesados); ?></p>
    
          <?php if (!in_array($d->p->status, ['recibido','cancelado','completado'])): ?>
            <p><?php echo sprintf('Se estima para el <b>%s</b>.', format_date($d->p->fecha_inicio)); ?></p>
          <?php elseif (in_array($d->p->status, ['recibido','procesando','completado'])): ?>
            <p><?php echo sprintf('Llegó el <b>%s</b>.', format_date($d->p->fecha_entrega)); ?></p>
          <?php endif; ?>
        </div>
      </div>

      <!-- información del proveedor -->
      <h5 class="mb-4">Información general</h5>

      <!-- Proveedor -->
      <div class="card shadow mb-3">
        <div class="card-header">Información del proveedor</div>
        <div class="card-body">
          <div class="mb-3">
            <label for="id_proveedor">Razón Social</label>
            <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $d->p->razon_social; ?>" disabled>
          </div>
          <div class="mb-3">
            <label for="nombre">Contacto</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $d->p->nombre; ?>" disabled>
          </div>
          <!-- <div class="mb-3">
            <label for="rfc">RFC</label>
            <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $d->p->rfc; ?>" disabled>
          </div>
          <div class="mb-3">
            <label for="email">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $d->p->email; ?>" disabled>
          </div>
          <div class="mb-3">
            <label for="telefono">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $d->p->telefono; ?>" disabled>
          </div>
          <div class="mb-3">
            <label for="direccion">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $d->p->direccion; ?>" disabled>
          </div> -->
        </div>
      </div>

      <!-- Pago del pedido -->
      <div class="card shadow mb-3">
        <div class="card-header">Pago del pedido</div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-6">
              <label for="total_pagado" class="form-label">Total pagado</label>
              <p><?php echo sprintf('<span class="badge rounded-pill %s">%s</span>',
                $d->p->total_pagado > 0 ? 'bg-success' : 'bg-danger',
                money($d->p->total_pagado, '$')); ?></p>
            </div>
            <div class="col-12 col-md-6">
              <label for="metodo_pago" class="form-label">Método de pago</label>
              <p><?php echo format_metodo_pago($d->p->metodo_pago); ?></p>
            </div>
            <div class="col-12 col-md-6">
              <label for="status_pago" class="form-label">Estado del pago</label>
              <p><?php echo format_estado_pago($d->p->status_pago); ?></p>
            </div>
  
            <?php if (in_array($d->p->status_pago, ['pagado','parcial','reembolsado'])): ?>
              <div class="col-12 col-md-6">
                <label for="fecha_pago" class="form-label">Fecha del pago</label>
                <p><?php echo format_date($d->p->fecha_pago); ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Notas del pedido -->
      <?php if (!empty($d->p->notas)): ?>
        <div class="mb-3">
          <div class="alert alert-primary">
            <p><b>Notas del pedido</b></p>
            <p><?php echo nl2br($d->p->notas); ?></p>
          </div>
        </div>
      <?php endif; ?>

      <!-- Adjuntos del pedido -->
      <div class="card shadow mb-3">
        <div class="card-header">Imágenes adjuntas</div>
        <div class="card-body">
          <?php if (empty($d->p->adjuntos)): ?>
            <div class="text-center text-muted p-5"><i class="fas fa-images"></i> No hay adjuntos en este pedido.</div>
          <?php else: ?>
            <div class="row gx-2">
              <?php foreach ($d->p->adjuntos as $adjunto): ?>
                <div class="col-xl-4 col-md-6 col-12 mb-2 position-relative overflow-hidden">
                  <?php echo sprintf('<a href="%s" data-lightbox="adjuntos" data-title="%s">', $adjunto->permalink, 'Adjunto: '.$adjunto->contenido); ?>
                  <?php echo sprintf('<img class="img-thumbnail img-fluid" src="%s" alt="%s">', UPLOADED.$adjunto->contenido, $adjunto->contenido); ?>
                  <?php echo '</a>'; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <span class="text-muted"><?php echo sprintf('%s %s', get_sitename(), get_version()); ?></span>
    </div>

    <!-- Tabla de productos en el pedido -->
    <div class="col-xl-8 col-lg-6 col-md-12 col-12">
      <?php if (empty($d->p->productos)): ?>
        <div class="w-100 py-5 text-center">
          <img src="<?php echo IMAGES.'file.png'; ?>" alt="Sin registros" class="img-fluid" style="width: 150px;">
          <h4 class="mt-3 text-muted">No hay productos</h4>
          <p class="text-muted">Lo sentimos, no encontramos nada por aquí.</p>
        </div>
      <?php else: ?>
        <div class="card">
          <div class="card-header clearfix">Productos en pedido
          <div class="btn-group float-end">
            <a href="<?php echo buildURL(sprintf('p/export/%s', $d->p->id)); ?>" class="btn btn-success btn-sm"><i class="fas fa-file-csv"></i> Exportar como CSV</a>
          </div>
          </div>
          <div class="card-body">
            <div class="table-responsive" style="height: 100%;">
              <table class="table table-striped align-middle">
                <thead>
                  <tr>
                    <th width="10%"></th>
                    <th>Producto</th>
                    <th class="text-center">Precio</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-center">Recibidos</th>
                    <th class="text-center">Subtotal</th>
                    <th class="text-end">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($d->p->productos as $p): ?>
                    <?php $total_procesados = $p->recibidos + $p->danados + $p->cancelados; ?>

                    <tr data-id="<?php echo $p->id; ?>" class="<?php echo in_array($d->p->status, ['procesando', 'completado']) ? format_clases_producto_pedido($p->cantidad, $total_procesados) : null; ?>">
                      <td>
                      <?php if (is_file(UPLOADS.$p->imagen)): ?>
                        <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">', ASSETS.'uploads/'.$p->imagen, $p->nombre); ?>
                      <?php else: ?>
                        <?php echo sprintf('<img src="%s" alt="Sin imagen" class="img-fluid" style="width: 50px; height: 50px;">', IMAGES.'noimage.jpg'); ?>
                      <?php endif; ?>
                      </td>
                      <td>
                        <?php echo $p->nombre; ?>
                        <small class="d-block text-muted"><?php echo sprintf('Atributo: %s', $p->corte); ?></small>
                        <?php if (!empty($p->sku)): ?>
                          <small class="d-block text-muted"><?php echo sprintf('SKU: %s', $p->sku); ?></small>
                        <?php endif; ?>
                        <?php if (!empty($p->adjuntos) && in_array($d->p->status, ['pendiente','borrador'])): ?>
                          <small class="d-block text-muted"><?php echo sprintf('<a href="%s" target="_blank"><i class="fas fa-download"></i> Descargar</a>', $p->adjuntos); ?></small>
                        <?php endif; ?>
                      </td>
                      <td align="center"><?php echo money($p->precio); ?></td>
                      <td width="10%" align="center">
                        <?php echo $p->cantidad; ?>
                      </td>
                      <td width="10%" align="center">
                        <?php echo $total_procesados; ?>

                        <?php if ($p->danados > 0 || $p->cancelados > 0): ?>
                          <?php echo more_info(format_stock_damage($p->danados, $p->cancelados), 'text-danger') ?>
                        <?php endif; ?>
                      </td>
                      <td align="center"><?php echo money($p->subtotal); ?></td>
                      <td align="right"><?php echo money($p->total); ?></td>
                    </tr>
                  <?php endforeach; ?>
                  <tr>
                    <td colspan="3">Resumen</td>
                    <td align="center"><b><?php echo $d->p->total_cantidades; ?></b></td>
                    <td align="center"><b><?php echo $d->p->total_procesados; ?></b></td>
                    <td align="center"><b><?php echo money($d->p->subtotal); ?></b></td>
                    <td align="right"><b><?php echo money($d->p->total); ?></b></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>