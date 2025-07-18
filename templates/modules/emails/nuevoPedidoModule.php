<p><?php echo sprintf('Hola <b>%s</b>, esta es una notificación para hacerte saber que se ha solicitado un nuevo pedido con número <b>#%s</b>, que incluye un total de <b>%s</b> unidades.<br><br>
¡Saludos!<br>', 
$d->nombre,
$d->numero,
$d->total_cantidades
); ?></p>

<p>Para ver el pedido en línea visita el siguiente enlace:</p>
<a href="<?php echo $d->url; ?>" class="btn btn-success" style="background: orange; color: white; padding: 10px; text-decoration: none;">Ver en línea</a>
<br><br>

<?php if (!empty($d->productos)): ?>
  <table class="table table-striped align-middle" style="vertical-align: middle;">
    <thead>
      <tr>
        <th width="10%"></th>
        <th>Producto</th>
        <th class="text-center">Precio</th>
        <th class="text-center">Cantidad</th>
        <th class="text-end">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($d->productos as $p): ?>
        <tr data-id="<?php echo $p->id; ?>">
          <td>
          <?php if (is_file(UPLOADS.$p->imagen)): ?>
            <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">', UPLOADED.$p->imagen, $p->nombre); ?>
          <?php else: ?>
            <?php echo sprintf('<img src="%s" alt="Sin imagen" class="img-fluid" style="width: 50px; height: 50px;">', IMAGES.'noimage.jpg'); ?>
          <?php endif; ?>
          </td>
          <td>
            <?php echo $p->nombre; ?>
            <small style="display: block; color: grey"><?php echo sprintf('Atributo: %s', $p->corte); ?></small>
            <?php if (!empty($p->sku)): ?>
              <small style="display: block; color: grey"><?php echo sprintf('SKU: %s', $p->sku); ?></small>
            <?php endif; ?>
          </td>
          <td align="center"><?php echo money($p->precio); ?></td>
          <td width="10%" align="center"><?php echo $p->cantidad; ?></td>
          <td align="right"><?php echo money($p->total); ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="3">Resumen</td>
        <td align="center"><b><?php echo $d->total_cantidades; ?></b></td>
        <td align="right"><b><?php echo money($d->total); ?></b></td>
      </tr>
    </tbody>
  </table>
<?php else: ?>
  <p>No hay productos en este pedido.</p>
<?php endif; ?>