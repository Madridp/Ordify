<h5 class="mb-4">Información general</h5>

<!-- Tabs del pedido -->
<div class="card shadow mb-3">
  <div class="card-header pb-0">
    <ul class="nav nav-tabs" id="tabs_info_pedido" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="proveedor-tab" data-bs-toggle="tab" data-bs-target="#proveedor-tab-pane" type="button" role="tab" aria-controls="proveedor-tab-pane" aria-selected="true">Proveedor</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pago-tab" data-bs-toggle="tab" data-bs-target="#pago-tab-pane" type="button" role="tab" aria-controls="pago-tab-pane" aria-selected="true">Pago</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="notas-tab" data-bs-toggle="tab" data-bs-target="#notas-tab-pane" type="button" role="tab" aria-controls="notas-tab-pane" aria-selected="false">Notas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="adjuntos-tab" data-bs-toggle="tab" data-bs-target="#adjuntos-tab-pane" type="button" role="tab" aria-controls="adjuntos-tab-pane" aria-selected="false">Adjuntos</button>
      </li>
    </ul>
  </div>
  <div class="card-body tab-content" id="tabs_info_pedido_content">
    <!-- Proveedor del pedido -->
    <div class="tab-pane fade show active" id="proveedor-tab-pane" role="tabpanel" aria-labelledby="proveedor-tab" tabindex="0">
      <?php if (empty($d->proveedores)): ?>
        <div class="alert alert-danger mb-3">
          <p>No hay proveedores registrados en Ordify, por favor agrega uno para continuar.</p>
          <a href="proveedores/agregar" class="btn btn-danger">Agregar proveedor</a>
        </div>
      <?php else: ?>
        <!-- Información del proveedor -->
        <div class="mb-3">
          <label for="id_proveedor">Razón Social</label>
          <?php if ($d->status === 'borrador'): ?>
            <select class="form-select do_update_proveedor_pedido" name="id_proveedor" id="id_proveedor" data-id="<?php echo $d->id; ?>">
              <?php foreach ($d->proveedores as $prv): ?>
                <?php echo sprintf('<option value="%s" %s>%s</option>', $prv->id, $prv->id == $d->id_proveedor ? 'selected' : '', $prv->razon_social) ?>
              <?php endforeach; ?>
            </select>
          <?php else: ?>
            <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $d->razon_social; ?>" disabled>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <label for="nombre">Proveedor</label>
          <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $d->nombre; ?>" disabled>
        </div>
        <div class="mb-3">
          <label for="rfc">RFC</label>
          <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $d->rfc; ?>" disabled>
        </div>
        <div class="mb-3">
          <label for="email">Correo electrónico</label>
          <input type="email" class="form-control" id="email" name="email" value="<?php echo $d->email; ?>" disabled>
        </div>
        <div class="mb-3">
          <label for="telefono">Teléfono</label>
          <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $d->telefono; ?>" disabled>
        </div>
        <div class="mb-3">
          <label for="direccion">Dirección</label>
          <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $d->direccion; ?>" disabled>
        </div>
        <div class="mb-3">
          <label for="creado">Creado</label>
          <input type="text" class="form-control" id="creado" name="creado" value="<?php echo format_date($d->creado); ?>" disabled>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pago del pedido -->
    <div class="tab-pane fade" id="pago-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
      <form class="do_pay_pedido">
        <div class="mb-3 row">
          <div class="col-12 col-sm-4">
            <label for="total_pagado" class="form-label">Total pagado</label>
            <input type="text" class="form-control" placeholder="$0.00" value="<?php echo money($d->total_pagado, ''); ?>" id="total_pagado" name="total_pagado" required>
          </div>
          <div class="col-12 col-sm-4">
            <label for="metodo_pago" class="form-label">Método de pago</label>
            <select name="metodo_pago" id="metodo_pago" class="form-select">
              <option value="none">Selecciona una opción...</option>
              <?php foreach (get_metodos_pago() as $metodo_pago): ?>
                <?php echo sprintf('<option value="%s" %s>%s</option>',
                $metodo_pago['slug'],
                $metodo_pago['slug'] == $d->metodo_pago ? 'selected' : null,
                $metodo_pago['name']
                ); ?>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-sm-4">
            <label for="status_pago" class="form-label">Estado del pago</label>
            <select name="status_pago" id="status_pago" class="form-select">
              <option value="none">Selecciona una opción...</option>
              <?php foreach (get_status_pago() as $status_pago): ?>
                <?php echo sprintf('<option value="%s" %s>%s</option>',
                $status_pago['slug'],
                $status_pago['slug'] == $d->status_pago ? 'selected' : null,
                $status_pago['name']
                ); ?>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <?php if (in_array($d->status_pago, ['pagado','parcial','reembolsado'])): ?>
          <div class="mb-3">
            <label for="fecha_pago" class="form-label">Fecha del pago</label>
            <input type="date" class="form-control readonly" readonly disabled value="<?php echo date('Y-m-d', strtotime($d->fecha_pago)); ?>">
          </div>
        <?php endif; ?>

        <div class="mb-0">
          <input type="hidden" name="id" value="<?php echo $d->id; ?>" required>
          <button class="btn btn-success btn-sm"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>
      </form>
    </div>
    
    <!-- Notas del pedido -->
    <div class="tab-pane fade" id="notas-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
      <div class="alert alert-primary clearfix">
        <p><b>Notas del pedido</b></p>
        <?php if (!empty($d->notas)): ?>
          <p><?php echo nl2br($d->notas); ?></p>
        <?php else: ?>
          <p>No hay notas agregadas al pedido actual.</p>
        <?php endif; ?>
        <form method="post" class="do_update_notas_pedido">
          <?php echo insert_inputs(); ?>
          <input type="hidden" name="id" value="<?php echo $d->id; ?>">

          <div class="mb-3" style="display: none;">
            <label for="notas">Notas *</label>
            <textarea name="notas" id="notas" cols="5" rows="5" class="form-control"><?php echo $d->notas; ?></textarea>
          </div>
        
          <button class="btn btn-sm btn-primary float-end do_open_update_notas_pedido" type="button"><i class="fas fa-edit"></i> Editar</button>
          
          <button class="btn btn-sm btn-danger float-start do_reset_update_notas_pedido" type="reset" style="display: none;">Cancelar</button>
          <button class="btn btn-sm btn-success float-end" type="submit" style="display: none;"><i class="fas fa-save"></i> Guardar cambios</button>
        </form>
      </div>
    </div>

    <!-- Adjuntos del pedido -->
    <div class="tab-pane fade" id="adjuntos-tab-pane" role="tabpanel" aria-labelledby="adjuntos-tab" tabindex="0">
      <form class="mb-3" action="pedidos/post_adjuntos" enctype="multipart/form-data" method="post">
        <?php echo insert_inputs(); ?>
        <input type="hidden" name="id" value="<?php echo $d->id; ?>" required>

        <div class="mb-3">
          <label for="imagenes" class="form-label">Selecciona una o más imágenes *</label>
          <input type="file" class="form-control" id="imagenes" name="imagenes[]" accept="image/png, image/jpeg" multiple required>
        </div>

        <button class="btn btn-success btn-sm"><i class="fas fa-save"></i> Guardar cambios</button>
      </form>
      <hr>
      <?php if (empty($d->adjuntos)): ?>
        <div class="text-center text-muted p-5"><i class="fas fa-images"></i> No hay adjuntos en este pedido.</div>
      <?php else: ?>
        <div class="row gx-2">
          <?php foreach ($d->adjuntos as $adjunto): ?>
            <div class="col-xl-4 col-md-6 col-12 mb-2 position-relative overflow-hidden">
              <?php echo sprintf('<a href="%s" data-lightbox="adjuntos" data-title="%s">', $adjunto->permalink, 'Adjunto: '.$adjunto->contenido); ?>
              <?php echo sprintf('<img class="img-thumbnail img-fluid" src="%s" alt="%s">', UPLOADED.$adjunto->contenido, $adjunto->contenido); ?>
              <?php echo sprintf('<a href="%s" class="btn btn-danger btn-sm position-absolute top-0 end-0 confirmar"><i class="fa fa-trash"></i></a>', 
                buildURL('pedidos/borrar-adjunto/'.$adjunto->id, ['id_pedido' => $d->id])); ?>
              <?php echo '</a>'; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Cambio de estado del pedido -->
<div class="card shadow mb-3">
  <div class="card-header">Cambio de estado del pedido</div>
  <div class="card-body">
    <?php if ($d->status === 'borrador'): ?>
      <a href="<?php echo sprintf('pedidos/realizar/%s', $d->id); ?>" class="btn btn-success confirmar"><i class="fas fa-envelope"></i> Realizar Pedido</a>
    <?php elseif ($d->status === 'pendiente'): ?>
      <a href="<?php echo sprintf('pedidos/en-camino/%s', $d->id); ?>" class="btn btn-primary text-white confirmar"><i class="fas fa-truck"></i> En Camino</a>
      <a href="<?php echo sprintf('pedidos/cancelar/%s', $d->id); ?>" class="btn btn-danger confirmar"><i class="fas fa-ban"></i> Cancelar</a>
    <?php elseif ($d->status === 'en_camino'): ?>
      <a href="<?php echo sprintf('pedidos/recibir/%s', $d->id); ?>" class="btn btn-primary text-white confirmar"><i class="fas fa-pallet"></i> Recibido</a>
    <?php elseif ($d->status === 'recibido'): ?>
      <a href="<?php echo sprintf('pedidos/procesar/%s', $d->id); ?>" class="btn btn-primary text-white confirmar"><i class="fas fa-truck-loading"></i> Procesar</a>
    <?php elseif ($d->status === 'procesando'): ?>
      <a href="<?php echo sprintf('pedidos/completar/%s', $d->id); ?>" class="btn btn-success text-white confirmar"><i class="fas fa-check"></i> Completar</a>
    <?php elseif ($d->status === 'completado'): ?>
      <p>El pedido actual fue recibido y procesado con éxito.</p>
    <?php elseif ($d->status === 'cancelado'): ?>
      <p>El pedido actual fue cancelado.</p>
    <?php endif; ?>
  </div>
</div>



