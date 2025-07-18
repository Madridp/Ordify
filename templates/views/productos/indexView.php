<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="productos/atributos" class="btn btn-sm btn-outline-success"><i class="fas fa-list"></i> Atributos</a>
      <a href="productos/agregar" class="btn btn-sm btn-outline-success"><i class="fas fa-plus"></i> Agregar</a>
    </div>
  </div>
</div>

<?php if (empty($d->productos->rows)): ?>
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
          <th width="5%"></th>
          <th>Nombre</th>
          <th>Atributo</th>
          <th>Variantes</th>
          <th class="text-center">Precio</th>
          <th class="text-end">Creado</th>
          <th class="text-end">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($d->productos->rows as $p): ?>
          <tr>
            <td>
              <?php if (is_file(UPLOADS.$p->imagen)): ?>
                <?php echo sprintf('<img src="%s" alt="%s" class="img-fluid" style="width: 50px; height: 50px; object-fit: cover;">', ASSETS.'uploads/'.$p->imagen, $p->nombre); ?>
              <?php else: ?>
                <?php echo sprintf('<img src="%s" alt="Sin imagen" class="img-fluid" style="width: 50px; height: 50px;">', IMAGES.'noimage.jpg'); ?>
              <?php endif; ?>
            </td>
            <td>
              <?php echo sprintf('<a href="productos/ver/%s">%s</a>', $p->id, $p->nombre); ?>
              <?php echo !empty($p->sku) ? sprintf('<span class="d-block text-muted">SKU: %s</span>', $p->sku) : ''; ?>
            </td>
            <td><?php echo $p->corte; ?></td>
            <td><?php echo format_variantes($p->variantes); ?></td>
            <td class="text-center"><?php echo money($p->precio); ?></td>
            <td class="text-end"><?php echo format_date($p->creado); ?></td>
            <td class="text-end">
              <button type="button" class="btn btn-sm btn-outline-muted" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="visually-hidden">Toggle Dropdown</span>
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <?php if (is_admin()): ?>
                  <a class="dropdown-item confirmar" href="<?php echo buildURL(sprintf('productos/duplicar/%s', $p->id)); ?>"><i class="fas fa-copy"></i> Duplicar</a>     
                <?php endif; ?>
                <a class="dropdown-item" href="<?php echo sprintf('productos/editar/%s', $p->id); ?>"><i class="fas fa-edit"></i> Editar</a>
                <a class="dropdown-item confirmar" href="<?php echo buildURL(sprintf('productos/borrar/%s', $p->id)); ?>"><i class="fas fa-trash"></i> Borrar</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php echo $d->productos->pagination; ?>
<?php endif; ?>

<?php require_once INCLUDES.'inc_footer.php'; ?>