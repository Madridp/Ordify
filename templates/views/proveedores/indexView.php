<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="proveedores/agregar" class="btn btn-sm btn-outline-success"><i class="fas fa-plus"></i> Agregar</a>
    </div>
  </div>
</div>

<?php if (empty($d->proveedores->rows)): ?>
  <div class="w-100 py-5  text-center">
    <img src="<?php echo IMAGES.'file.png'; ?>" alt="Sin registros" class="img-fluid" style="width: 150px;">
    <h4 class="mt-3 text-muted">No hay registros</h4>
    <p class="text-muted">Lo sentimos, no encontramos nada por aquí.</p>
  </div>
<?php else: ?>
  <div class="table-responsive" style="height: 100%;">
    <table class="table table-striped table align-middle">
      <thead>
        <tr>
          <th>Razón Social</th>
          <th>RFC</th>
          <th>Contacto</th>
          <th>Creado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($d->proveedores->rows as $p): ?>
          <tr>
            <td><?php echo sprintf('<a href="proveedores/ver/%s">%s</a>', $p->id, $p->razon_social); ?></td>
            <td><?php echo $p->rfc; ?></td>
            <td><?php echo $p->nombre; ?></td>
            <td><?php echo format_date($p->creado); ?></td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-muted" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?php echo sprintf('proveedores/editar/%s', $p->id); ?>"><i class="fas fa-edit"></i> Editar</a></li>
                <li><a class="dropdown-item confirmar" href="<?php echo buildURL(sprintf('proveedores/borrar/%s', $p->id)); ?>"><i class="fas fa-trash"></i> Borrar</a></li>
              </ul>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  
  <?php echo $d->proveedores->pagination; ?>
<?php endif; ?>

<?php require_once INCLUDES.'inc_footer.php'; ?>