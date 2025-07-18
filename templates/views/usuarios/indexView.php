<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="usuarios/agregar" class="btn btn-sm btn-outline-success"><i class="fas fa-plus"></i> Agregar</a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12 col-12">
    <?php if (empty($d->usuarios->rows)): ?>
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
              <th>Nombre</th>
              <th>Usuario</th>
              <th>Role</th>
              <th>Correo</th>
              <th>Creado</th>
              <th class="text-end">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($d->usuarios->rows as $u): ?>
              <tr>
                <td><?php echo $u->nombre; ?></td>
                <td><?php echo $u->usuario; ?></td>
                <td><?php echo format_user_role($u->role); ?></td>
                <td><?php echo $u->email; ?></td>
                <td><?php echo format_date($u->creado); ?></td>
                <td class="text-end">
                  <?php if (is_admin()): ?>
                    <?php if ($u->id == get_user('id')): ?>
                      <a href="perfil" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> Mi perfil</a>               
                    <?php else: ?>
                      <a href="<?php echo sprintf('usuarios/ver/%s', $u->id); ?>" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                      <a href="<?php echo buildURL(sprintf('usuarios/borrar/%s', $u->id)); ?>" class="btn btn-sm btn-danger confirmar"><i class="fas fa-trash"></i></a>
                    <?php endif; ?>
                  <?php else: ?>
                  
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <?php echo $d->usuarios->pagination; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>