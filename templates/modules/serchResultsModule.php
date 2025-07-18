<div class="py-5">
  <div class="card">
    <div class="card-header">Resultados</div>
    <div class="card-body">
      <?php if (empty($d->proveedores) && empty($d->pedidos) && empty($d->productos)): ?>
        <?php echo sprintf('Sin resultados para <b>%s</b>, intenta de nuevo.', $d->term) ?>
      <?php endif; ?>

      <?php if (!empty($d->proveedores)): ?>
        <div class="mb-3">
          <p>Proveedores</p>
          <table class="table table-striped">
            <tbody>
              <?php foreach ($d->proveedores as $pr): ?>
                <tr>
                  <td><?php echo sprintf('<a href="proveedores/ver/%s">%s</a>', $pr->id, $pr->razon_social); ?></td>
                  <td><?php echo $pr->nombre; ?></td>
                  <td><?php echo $pr->email; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <?php if (!empty($d->productos)): ?>
        <div class="mb-3">
          <p>Productos</p>
          <table class="table table-striped">
            <tbody>
              <?php foreach ($d->productos as $pro): ?>
                <tr>
                  <td><?php echo sprintf('<a href="productos/ver/%s">%s</a>', $pro->id, $pro->nombre); ?></td>
                  <td><?php echo money($pro->precio); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <?php if (!empty($d->pedidos)): ?>
        <div class="mb-3">
          <p>Pedidos</p>
          <table class="table table-striped">
            <tbody>
              <?php foreach ($d->pedidos as $p): ?>
                <tr>
                  <td><?php echo sprintf('<a href="pedidos/ver/%s">%s</a>', $p->id, $p->numero); ?></td>
                  <td><?php echo $p->razon_social; ?></td>
                  <td><?php echo $p->nombre; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>