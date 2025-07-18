<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="productos" class="btn btn-sm btn-outline-success"><i class="fas fa-undo"></i> Regresar</a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-12">
    <form action="productos/post_agregar_atributo" method="post">
      <?php echo insert_inputs(); ?>
      
      <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>
  
      <div class="mb-3">
        <label for="atributo" class="form-label">Título del atributo *</label>
        <input type="text" class="form-control" id="atributo" name="atributo" placeholder="Manga corta" required>
      </div>

      <button class="btn btn-success" type="submit">Guardar</button>
    </form>
  </div>

  <div class="col-md col-12">
    <?php if (empty($d->atributos->rows)): ?>
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
              <th>Título</th>
              <th class="text-center">Creado</th>
              <th class="text-end">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($d->atributos->rows as $v): ?>
              <tr>
                <td><?php echo $v->titulo; ?></td>
                <td class="text-center"><?php echo format_date($v->created_at); ?></td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-outline-muted" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo sprintf('productos/editar-atributo/%s', $v->id); ?>"><i class="fas fa-edit"></i> Editar</a></li>
                    <li><a class="dropdown-item confirmar" href="<?php echo buildURL(sprintf('productos/borrar-atributo/%s', $v->id)); ?>"><i class="fas fa-trash"></i> Borrar</a></li>
                  </ul>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    
      <?php echo $d->atributos->pagination; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>