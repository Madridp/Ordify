<?php if (empty($d->productos)): ?>
  <div class="w-100 py-5 text-center">
    <img src="<?php echo IMAGES.'file.png'; ?>" alt="Sin registros" class="img-fluid" style="width: 150px;">
    <h4 class="mt-3 text-muted">No hay productos</h4>
    <p class="text-muted">Lo sentimos, no encontramos nada por aquí.</p>
  </div>
<?php else: ?>
  <div class="card">
    <div class="card-header">
      Productos en pedido

      <button class="btn btn-success btn-sm recalcular_precios float-end" data-id="<?php echo $d->id; ?>"><i class="fas fa-redo"></i> Recalcular</button>
    </div>
    <div class="card-body">
      <?php if (!in_array($d->status, ['procesando'])): ?>
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
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="3">Resumen</td>
                <td align="center"><b><?php echo $d->total_cantidades; ?></b></td>
                <td align="center"><b><?php echo $d->total_procesados; ?></b></td>
                <td align="center"><b><?php echo money($d->subtotal); ?></b></td>
                <td align="right"><b><?php echo money($d->total); ?></b></td>
                <td></td>
              </tr>
              <?php $danados = []; // para almacenar los productos dañados ?>
              <?php foreach ($d->productos as $p): ?>
                <?php $total_procesados = $p->recibidos + $p->danados + $p->cancelados; ?>

                <?php if ($p->danados > 0): // Registro solo si el producto tiene dañados ?>
                  <?php $danados[] = $p; ?>
                <?php endif; ?>

                <tr data-id="<?php echo $p->id; ?>" class="<?php echo in_array($d->status, ['procesando', 'completado']) ? format_clases_producto_pedido($p->cantidad, $total_procesados) : null; ?>">
                  <td>
                  <?php if (is_file(UPLOADS.$p->imagen)): ?>
                    <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">', ASSETS.'uploads/'.$p->imagen, $p->nombre); ?>
                  <?php else: ?>
                    <?php echo sprintf('<img src="%s" alt="Sin imagen" class="img-fluid" style="width: 50px; height: 50px;">', IMAGES.'noimage.jpg'); ?>
                  <?php endif; ?>
                  </td>
                  <td>
                    <?php echo sprintf('<a href="productos/ver/%s" target="_blank">%s</a>', $p->id_producto, $p->nombre); ?>
                    <small class="d-block text-muted"><?php echo sprintf('Atributo: %s', $p->corte); ?></small>
                    <?php if (!empty($p->sku)): ?>
                      <small class="d-block text-muted"><?php echo sprintf('SKU: %s', $p->sku); ?></small>
                    <?php endif; ?>
                    <?php if (!empty($p->adjuntos)): ?>
                      <small class="d-block text-muted"><?php echo sprintf('<a href="%s" target="_blank"><i class="fas fa-download"></i> Descargar</a>', $p->adjuntos); ?></small>
                    <?php endif; ?>
                  </td>
                  <td align="center"><?php echo money($p->precio); ?></td>
                  <td width="10%" align="center">
                    <?php if (in_array($d->status, ['borrador'])): ?>
                      <input type="hidden" class="form-control" name="cantidad_original" value="<?php echo $p->cantidad; ?>" min="0">
                      <input type="number" class="form-control do_update_cantidad" name="cantidad" value="<?php echo $p->cantidad; ?>" min="0">
                    <?php else: ?>
                      <?php echo $p->cantidad; ?>
                    <?php endif; ?>
                  </td>
                  <td width="10%" align="center">
                    <?php echo $total_procesados; ?>

                    <?php if ($p->danados > 0 || $p->cancelados > 0): ?>
                      <?php echo more_info(format_stock_damage($p->danados, $p->cancelados), 'text-danger') ?>
                    <?php endif; ?>
                  </td>
                  <td align="center"><?php echo money($p->subtotal); ?></td>
                  <td align="right"><?php echo money($p->total); ?></td>
                  <td>
                    <?php if ($d->status === 'borrador'): ?>
                      <button class="btn btn-sm btn-danger do_delete_producto_de_pedido" data-id="<?php echo $p->id; ?>" type="button"><i class="fas fa-times"></i></button>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr>
                <td colspan="3">Resumen</td>
                <td align="center"><b><?php echo $d->total_cantidades; ?></b></td>
                <td align="center"><b><?php echo $d->total_procesados; ?></b></td>
                <td align="center"><b><?php echo money($d->subtotal); ?></b></td>
                <td align="right"><b><?php echo money($d->total); ?></b></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>

        <?php if ($d->status === 'completado' && !empty($danados)): // se mostrará solo si hay cargos a deducir por daños ?>
          <h5>Productos dañados procesados</h5>
          <div class="table-responsive" style="height: 100%;">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th width="10%"></th>
                  <th>Producto</th>
                  <th class="text-center">Precio</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">Subtotal</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php $cargos = 0; ?>
                <?php foreach ($danados as $pd): ?>
                  <tr>
                    <td>
                      <?php if (is_file(UPLOADS.$pd->imagen)): ?>
                        <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">', ASSETS.'uploads/'.$pd->imagen, $pd->nombre); ?>
                      <?php else: ?>
                        <?php echo sprintf('<img src="%s" alt="Sin imagen" class="img-fluid" style="width: 50px; height: 50px;">', IMAGES.'noimage.jpg'); ?>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php echo sprintf('<a href="productos/ver/%s" target="_blank">%s</a>', $pd->id_producto, $pd->nombre); ?>
                      <small class="d-block text-muted"><?php echo sprintf('Atributo: %s', $pd->corte); ?></small>
                      <?php if (!empty($pd->sku)): ?>
                        <small class="d-block text-muted"><?php echo sprintf('SKU: %s', $pd->sku); ?></small>
                      <?php endif; ?>
                    </td>
                    <td align="center"><?php echo money($pd->precio); ?></td>
                    <td width="10%" align="center"><?php echo $pd->danados; ?></td>
                    <?php $subtotal = $pd->precio * $pd->danados; ?>
                    <td align="center"><?php echo money($subtotal / 1.16); ?></td>
                    <td align="right"><?php echo money($subtotal); ?></td>
                    <?php $cargos = $cargos + $subtotal; ?>
                  </tr>
                <?php endforeach; ?>
                <tr>
                  <td colspan="3">Cargos generados</td>
                  <td align="center"><b><?php echo $d->total_rechazados; ?></b></td>
                  <td align="center"><b><?php echo money($cargos / 1.16); ?></b></td>
                  <td align="right"><b><?php echo money($cargos); ?></b></td>
                </tr>
                <tr>
                  <td colspan="2">Monto total a pagar</td>
                  <td colspan="4"align="right">
                    <?php echo sprintf('Total <b>%s</b> - Cargos <b>%s</b> = <b>%s</b>',
                      money($d->total),
                      money($cargos),
                      money($d->total - $cargos)
                      ); ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <!-- Procesando inventario recibido -->
        <div class="row">
          <?php foreach ($d->productos as $p): ?>
            <?php $total_procesados = $p->recibidos + $p->danados + $p->cancelados; ?>
            <div class="col-12 mb-1">
              <div class="card row-border <?php echo format_clases_producto_pedido($p->cantidad, $total_procesados); ?>" data-id="<?php echo $p->id; ?>">
                <div class="card-body">
                  <div class="row">
                    <div class="col-3 col-md-1">
                      <?php if (is_file(UPLOADS.$p->imagen)): ?>
                        <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">', ASSETS.'uploads/'.$p->imagen, $p->nombre); ?>
                      <?php else: ?>
                        <?php echo sprintf('<img src="%s" alt="Sin imagen" class="img-fluid" style="width: 50px; height: 50px;">', IMAGES.'noimage.jpg'); ?>
                      <?php endif; ?>
                    </div>
                    <div class="col-9 col-md-3 mb-1">
                      <?php echo sprintf('<a href="productos/ver/%s" target="_blank">%s</a>', $p->id_producto, $p->nombre); ?>
                      <small class="d-block text-muted"><?php echo sprintf('Atributo: %s', $p->corte); ?></small>
                      <?php if (!empty($p->sku)): ?>
                        <small class="d-block text-muted"><?php echo sprintf('SKU: %s', $p->sku); ?></small>
                      <?php endif; ?>
                    </div>
                    <div class="col-4 col-md-2">
                      <small class="text-muted d-block">Requeridos</small>
                      <b><?php echo $p->cantidad; ?></b>
                    </div>
                    <div class="col-4 col-md-2">
                      <small class="text-muted d-block">Procesados</small>
                      <b class="text-success"><?php echo $total_procesados; ?></b>
                    </div>
                    <div class="col-4 col-md-2 mb-1">
                      <small class="text-muted d-block">Con éxito</small>
                      <b class="text-primary"><?php echo $p->recibidos; ?></b>

                      <small class="text-muted d-block">Dañados</small>
                      <?php echo $p->danados > 0 ? sprintf('<b class="text-danger">%s</b>', $p->danados) : $p->danados; ?>

                      <small class="text-muted d-block">Cancelados</small>
                      <?php echo $p->cancelados > 0 ? sprintf('<b class="text-danger">%s</b>', $p->cancelados) : $p->cancelados; ?>
                    </div>
                    <div class="col-12 col-md-2 wrapper_update_recibidos">
                      <small class="text-muted d-block">Cantidad</small>
                      <div class="row">
                        <div class="col-12 mb-2">
                          <input type="hidden" class="form-control" name="cantidad_original_total" value="<?php echo $p->cantidad; ?>" min="0">
                          <input type="hidden" class="form-control" name="cantidad_original_recibidos" value="<?php echo $p->recibidos; ?>" min="0">
                          <input type="hidden" class="form-control" name="cantidad_original_danados" value="<?php echo $p->danados; ?>" min="0">
                          <input type="hidden" class="form-control" name="cantidad_original_cancelados" value="<?php echo $p->cancelados; ?>" min="0">
                          <input type="number" class="form-control form-control-sm text-center" name="recibidos" value="0" min="0" max="<?php echo $p->cantidad; ?>">
                        </div>
                        <div class="col-12 mb-2">
                          <select name="tipo_movimiento" id="tipo_ajuste" class="form-select form-select-sm text-center">
                            <?php foreach (get_stock_movement_types() as $mt): ?>
                              <?php echo sprintf('<option value="%s">%s</option>', $mt[0], $mt[1]); ?>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col-12">
                          <div class="d-grid gap-2">
                            <button class="btn btn-success btn-sm do_update_recibidos">Guardar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div id="test_wrapper"></div>
<?php endif; ?>