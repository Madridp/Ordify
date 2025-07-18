<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="pedidos/agregar" class="btn btn-sm btn-outline-success"><i class="fas fa-plus"></i> Agregar</a>
    </div>
  </div>
</div>

<?php if (empty($d->pedidos->rows)): ?>
  <div class="w-100 py-5  text-center">
    <img src="<?php echo IMAGES.'file.png'; ?>" alt="Sin registros" class="img-fluid" style="width: 150px;">
    <h4 class="mt-3 text-muted">No hay registros</h4>
    <p class="text-muted">Lo sentimos, no encontramos nada por aquí.</p>
  </div>
<?php else: ?>
  <div class="table-responsive" style="height: 100%;">
    <table class="table table-striped table-hover align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Proveedor</th>
          <th class="text-center">Unidades</th>
          <th class="text-center">Estado</th>
          <th class="text-center">Total</th>
          <th class="text-center">Médoto de pago</th>
          <th class="text-center">Estado del pago</th>
          <th class="text-center">Total pagado</th>
          <th class="text-end">Creado</th>
          <th class="text-end">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($d->pedidos->rows as $p): ?>
          <tr class="<?php echo $p->status === 'cancelado' ? 'bg-light text-muted' : ''; ?>">
            <td><?php echo sprintf('<a href="pedidos/ver/%s">%s</a>', $p->id, $p->numero); ?></td>
            <td><?php echo sprintf('<a href="proveedores/ver/%s">%s</a>', $p->id_proveedor, $p->razon_social); ?></td>
            <td class="text-center">
              <?php if (in_array($p->status, ['completado', 'procesando'])): ?>
                <?php echo sprintf('%s de %s', $p->total_procesados, $p->total_cantidades); ?>
              <?php else: ?>
                <?php echo $p->total_cantidades; ?>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php echo format_estado_pedido($p->status); ?>
              <?php if ($p->status === 'completado' && $p->total_procesados < $p->total_cantidades): ?>
                <span class="badge rounded-pill bg-warning text-dark"><i class="fas fa-table"></i> Con faltantes</span>
              <?php elseif ($p->status === 'completado' && $p->total_procesados > $p->total_cantidades): ?>
                <span class="badge rounded-pill bg-info text-dark"><i class="fas fa-table"></i> Con extras</span>
              <?php endif; ?>
            </td>
            <td class="text-center"><?php echo money($p->total); ?></td>
            <td class="text-center"><?php echo format_metodo_pago($p->metodo_pago); ?></td>
            <td class="text-center"><?php echo format_estado_pago($p->status_pago); ?></
            td>
            <td class="text-center"><?php echo money($p->total_pagado); ?></td>
            <td class="text-end"><?php echo format_date($p->creado); ?></td>
            <td class="text-end">
              <button type="button" class="btn btn-sm btn-outline-muted" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <?php if (is_admin()): ?>
                  <a class="dropdown-item confirmar" href="<?php echo sprintf('pedidos/duplicar/%s', $p->id); ?>"><i class="fas fa-copy"></i> Duplicar</a>     
                <?php endif; ?>
                
                <a class="dropdown-item" href="<?php echo sprintf('pedidos/ver/%s', $p->id); ?>"><i class="fas fa-edit"></i> Editar</a>

                <?php if (in_array($p->status, ['completado']) && is_admin()): ?>
                  <a class="dropdown-item confirmar" href="<?php echo buildURL(sprintf('pedidos/borrar/%s', $p->id), ['force_delete' => true]); ?>"><i class="fas fa-trash"></i> Forzar borrado</a>
                <?php elseif (is_admin()): ?>
                  <a class="dropdown-item confirmar" href="<?php echo buildURL(sprintf('pedidos/borrar/%s', $p->id)); ?>"><i class="fas fa-trash"></i> Borrar</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php echo $d->pedidos->pagination; ?>
<?php endif; ?>

<?php require_once INCLUDES.'inc_footer.php'; ?>