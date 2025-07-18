<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_navbar.php'; ?>
<?php require_once INCLUDES.'inc_sidebar.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><?php echo $d->title; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-group me-2">
      <a href="productos/atributos" class="btn btn-sm btn-outline-success"><i class="fas fa-undo"></i> Regresar</a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-12">
    <form action="productos/post_actualizar_atributo" method="post">
      <input type="hidden" name="id" value="<?php echo $d->v->id; ?>" required>
      <?php echo insert_inputs(); ?>
      
      <p class="text-muted">Completa el formulario, los campos con <span class="text-danger">*</span> son requeridos para continuar.</p>
  
      <div class="mb-3">
        <label for="atributo" class="form-label">Título del atributo *</label>
        <input type="text" class="form-control" id="atributo" name="atributo" 
        placeholder="Manga corta" 
        value="<?php echo $d->v->titulo; ?>"
        required>
      </div>

      <button class="btn btn-success" type="submit">Guardar</button>
    </form>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>