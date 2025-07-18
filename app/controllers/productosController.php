<?php
use Verot\Upload\Upload;

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de productos
 */
class productosController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
  }
  
  function index() {
    $data =
    [
      'title'     => 'Productos',
      'slug'      => 'productos',
      'productos' => productoModel::all_paginated()
    ];

    View::render('index', $data);
  }

  function agregar()
  {
    $data =
    [
      'title'       => 'Agregar producto',
      'slug'        => 'productos',
      'variantes'   => varianteModel::all()
    ];

    View::render('agregar', $data);
  }

  function post_agregar()
  {
    if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['nombre','precio','atributo','descripcion','sku','variantes'], $_POST)) {
      Flasher::deny();
      Redirect::back();
    }

    // Validación de información ingresada
    try {
      $imagen      = null;
      $nombre      = clean($_POST['nombre']);
      $descripcion = clean($_POST['descripcion']);
      $sku         = clean($_POST['sku']);
      $atributo    = clean($_POST['atributo']);
      $variantes   = clean($_POST['variantes']);
      $adjuntos    = clean($_POST['adjuntos']);
      $precio      = unmoney(clean($_POST['precio']));

      // Verificar que no exista el mismo SKU en la base de datos
      if (!empty($sku) && productoModel::list('productos', ['sku' => $sku])) {
        throw new PDOException(sprintf('Ya existe el SKU <b>%s</b> en la base de datos.'));
      }

      // Si no se ingresó SKU generar uno de forma dinámica desde 1.1.0
      if (empty($sku)) {
        $sku = random_password(10, 'numeric');
      }

      // Guardar imagen en el servidor si una es seleccionada
      if ($_FILES['imagen']['error'] !== 4 && !is_demo()) {
        $file    = $_FILES['imagen'];
        $handler = new Upload($file);
        
        if (!$handler->uploaded) {
          throw new Exception('Hubo un error al subir la imagen del producto.');
        }

        $handler->file_new_name_body = generate_filename();
        $handler->image_resize       = true;
        $handler->image_ratio_y      = true;
        $handler->image_x            = 200;
  
        $handler->process(UPLOADS);
        if (!$handler->processed) {
          throw new Exception($handler->error);
        }

        $imagen = $handler->file_dst_name; // nombre de la imagen guardada en el servidor
        $handler->clean();
      }

      // Seguridad para la versión demostración
      if ($_FILES['imagen']['error'] !== 4 && is_demo()) {
        Flasher::new('Por seguridad hemos desactivado la subida de archivos en la versión demostración.', 'danger');
      }

      $data =
      [
        'imagen'      => $imagen,
        'nombre'      => $nombre,
        'descripcion' => $descripcion,
        'sku'         => $sku,
        'corte'       => $atributo,
        'variantes'   => $variantes,
        'adjuntos'    => $adjuntos,
        'precio'      => $precio,
        'creado'      => now()
      ];

      // Guardar el registro en la db
      if (!$id = productoModel::add('productos', $data)) {
        throw new PDOException('Hubo un problema al guardar el producto.');
      }

      Flasher::new(sprintf('Nuevo producto <b>%s</b> agregado con éxito.', $nombre), 'success');
      Redirect::to('productos');

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function ver($id)
  {
    if (!$p = productoModel::by_id($id)) {
      Flasher::new('El producto no existe en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title' => sprintf('Viendo %s', $p['nombre']),
      'slug'  => 'productos',
      'p'     => $p
    ];

    View::render('ver', $data);
  }

  function editar($id)
  {
    if (!$p = productoModel::by_id($id)) {
      Flasher::new('El producto no existe en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'     => sprintf('Editando %s', $p['nombre']),
      'slug'      => 'productos',
      'p'         => $p,
      'atributos' => varianteModel::all()
    ];

    View::render('editar', $data);
  }

  function post_editar()
  {
    if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['id','nombre','precio','atributo','descripcion','sku','variantes'], $_POST)) {
      Flasher::deny();
      Redirect::back();
    }

    // Validación de información ingresada
    try {
      $id = clean($_POST['id']);

      // Validar que exista el producto en la base de datos
      if (!$producto = productoModel::by_id($id)) {
        throw new PDOException('No existe el producto en la base de datos.');
      }

      $imagen      = null;
      $imagen_     = $producto['imagen'];
      $nombre      = clean($_POST['nombre']);
      $descripcion = clean($_POST['descripcion']);
      $sku         = clean($_POST['sku']);
      $atributo    = clean($_POST['atributo']);
      $variantes   = clean($_POST['variantes']);
      $adjuntos    = clean($_POST['adjuntos']);
      $precio      = unmoney(clean($_POST['precio']));
      $delete_old  = false;

      // Validar atributo
      if ($atributo === 'none') {
        throw new Exception('Selecciona un atributo válido.');
      }

      // Verificar que no exista el mismo SKU en la base de datos
      $sql = 'SELECT * FROM productos WHERE id != :id AND sku = :sku';
      if (!empty($sku) && productoModel::query($sql, ['id' => $id, 'sku' => $sku])) {
        throw new PDOException(sprintf('Ya existe el SKU <b>%s</b> en la base de datos.'));
      }

      // Guardar imagen en el servidor si una es seleccionada
      if ($_FILES['imagen']['error'] !== 4 && !is_demo()) {
        $file    = $_FILES['imagen'];
        $handler = new Upload($file);
        
        if (!$handler->uploaded) {
          throw new Exception('Hubo un error al subir la imagen del producto.');
        }

        $handler->file_new_name_body = generate_filename();
        $handler->image_resize       = true;
        $handler->image_ratio_y      = true;
        $handler->image_x            = 200;
  
        $handler->process(UPLOADS);
        if (!$handler->processed) {
          throw new Exception($handler->error);
        }

        $imagen = $handler->file_dst_name; // nombre de la imagen guardada en el servidor
        $handler->clean();
      }

      // Seguridad para la versión demostración
      if ($_FILES['imagen']['error'] !== 4 && is_demo()) {
        Flasher::new('Por seguridad hemos desactivado la subida de archivos en la versión demostración.', 'danger');
      }

      $data =
      [
        'nombre'      => $nombre,
        'descripcion' => $descripcion,
        'sku'         => $sku,
        'corte'       => $atributo,
        'variantes'   => $variantes,
        'adjuntos'    => $adjuntos,
        'precio'      => $precio
      ];

      // Verificar el cambio de imagen del producto
      if ($imagen === null && empty($imagen_)) { // no existe imagen ni se ha subido nueva
        $data['imagen'] = $imagen;
      } else if (!empty($imagen_) && $imagen === null) { // ya existe imagen pero no se ha subido nueva
        $data['imagen'] = $imagen_;
      } else if (!empty($imagen_) && $imagen !== null) { // ya existe imagen pero se subió una nueva
        $data['imagen'] = $imagen;
        $delete_old     = true;
      } else if (empty($imagen_) && $imagen !== null) { // no existe imagen pero se subió una nueva
        $data['imagen'] = $imagen;
      }

      // Guardar el registro en la db
      if (!productoModel::update('productos', ['id' => $id], $data)) {
        throw new PDOException('Hubo un problema al actualizar el producto.');
      }

      // Borrar del disco duro la imagen anterior una vez actualizado el registro con éxito
      if ($delete_old === true && is_file(UPLOADS.$imagen_)) {
        unlink(UPLOADS.$imagen_);
      }

      Flasher::new(sprintf('Producto <b>%s</b> actualizado con éxito.', $nombre), 'success');
      Redirect::to('productos');

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function borrar($id)
  {
    try {
      // Proceso de borrado
      if (!Csrf::validate($_GET['_t'])) {
        Flasher::deny();
        Redirect::back();
      }

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada para el usuario.');
      }

      if (!$p = productoModel::by_id($id)) {
        throw new PDOException('El producto no existe en la base de datos.');
      }

      if (!productoModel::remove('productos', ['id' => $id], 1)) {
        throw new PDOException('Hubo un problema al borrar el registro.');
      }

      // Borrar imagen una vez borrado el registro
      if (is_file(UPLOADS.$p['imagen'])) {
        unlink(UPLOADS.$p['imagen']);
      }

      Flasher::new(sprintf('Producto <b>%s</b> borrado con éxito.', $p['nombre']), 'success');
      Redirect::to('productos');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function atributos()
  {
    $data =
    [
      'title'     => 'Atributos',
      'slug'      => 'productos',
      'atributos' => varianteModel::all_paginated(),
    ];

    View::render('atributos', $data);
  }

  function editar_atributo($id)
  {
    if (!$variante = varianteModel::by_id($id)) {
      Flasher::new('No existe la variante en la base de datos.', 'danger');
      Redirect::to('productos/atributos');
    }

    $data =
    [
      'title'     => sprintf('Editando %s', $variante['titulo']),
      'v'         => $variante,
      'slug'      => 'productos'
    ];

    View::render('editarAtributo', $data);
  }

  function post_agregar_atributo()
  {
    if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['atributo'], $_POST)) {
      Flasher::deny();
      Redirect::back();
    }

    // Validación de información ingresada
    try {
      $titulo = clean($_POST["atributo"]);

      // Si no se ingresó título de variante válido
      if (empty($titulo) || strlen($titulo) < 3) {
        throw new Exception('Ingresa un título de atributo válido.');
      }

      // Validar que no exista otro registro con el mismo título
      if (varianteModel::list(varianteModel::$t1, ['titulo' => $titulo, 'tipo' => 'variante'])) {
        throw new Exception(sprintf('Ya existe el atributo <b>%s</b> en la base de datos.', $titulo));
      }

      $data =
      [
        'tipo'         => 'variante',
        'id_padre'     => 0,
        'id_usuario'   => 0,
        'id_ref'       => 0,
        'titulo'       => $titulo,
        'permalink'    => '',
        'contenido'    => 'Atributo de producto',
        'status'       => 'public',
        'mime_type'    => '',
        'deadline_at'  => now(),
        'completed_at' => now(),
        'created_at'   => now()
      ];

      // Guardar el registro en la db
      if (!$id = varianteModel::add(varianteModel::$t1, $data)) {
        throw new PDOException('Hubo un problema al guardar el atributo.');
      }

      Flasher::new(sprintf('Nuevo atributo <b>%s</b> agregado con éxito.', $titulo), 'success');
      Redirect::to('productos/atributos');

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function post_actualizar_atributo()
  {
    if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['atributo','id'], $_POST)) {
      Flasher::deny();
      Redirect::back();
    }

    // Validación de información ingresada
    try {
      $id     = clean($_POST['id']);
      $titulo = clean($_POST["atributo"]);

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada para el usuario.');
      }

      // Validar que no exista otro registro con el mismo título
      $sql = 'SELECT * FROM posts WHERE tipo = "variante" AND titulo = :titulo AND id != :id';
      if (varianteModel::query($sql, ['titulo' => $titulo, 'id' => $id])) {
        throw new Exception(sprintf('Ya existe el atributo <b>%s</b> en la base de datos.', $titulo));
      }

      // Validar que exista la variante en la base de datos
      if (!$variante = varianteModel::by_id($id)) {
        throw new Exception('No existe el atributo en la base de datos.');
      }

      // Para validar cambios
      $db_titulo = $variante['titulo'];

      if ($db_titulo === $titulo) {
        Flasher::new('No hubo cambios en el registro.');
        Redirect::to('productos/atributos');
      }

      // Si no se ingresó título de variante válido
      if (empty($titulo) || strlen($titulo) < 3) {
        throw new Exception('Ingresa un título de atributo válido.');
      }

      $data =
      [
        'titulo' => $titulo
      ];

      // Actualizar registro en la db
      if (!varianteModel::update(varianteModel::$t1, ['id' => $id], $data)) {
        throw new PDOException('Hubo un problema al actualizar el atributo.');
      }

      Flasher::new(sprintf('Atributo <b>%s</b> actualizado con éxito.', $titulo), 'success');
      Redirect::to('productos/atributos');

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function borrar_atributo($id)
  {
    try {
      // Proceso de borrado
      if (!Csrf::validate($_GET['_t'])) {
        Flasher::deny();
        Redirect::back();
      }

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada para el usuario.');
      }

      if (!$v = varianteModel::by_id($id)) {
        throw new PDOException('El atributo no existe en la base de datos.');
      }

      if (!varianteModel::remove(varianteModel::$t1, ['id' => $id], 1)) {
        throw new PDOException('Hubo un problema al borrar el registro.');
      }

      Flasher::new(sprintf('Atributo <b>%s</b> borrado con éxito.', $v['titulo']), 'success');
      Redirect::back();

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function duplicar($id)
  {
    try {
      if (!Csrf::validate($_GET["_t"])) {
        throw new Exception('Acción no autorizada.');
      }

      // Validar que exista el producto
      if (!$producto = productoModel::by_id($id)) {
        throw new Exception('No existe el producto en la base de datos.');
      }

      // Validar si tiene imagen el producto actual
      if (is_file(UPLOADS.$producto['imagen'])) {
        $ext      = pathinfo(UPLOADS.$producto['imagen'], PATHINFO_EXTENSION);
        $filename = sprintf('%s.%s', generate_filename(), $ext);
        $copied   = copy(UPLOADS.$producto['imagen'], UPLOADS.$filename);

        if ($copied === true) {
          $producto['imagen'] = $filename;
        } else {
          $producto['imagen'] = null;
        }
      }

      // Generar nuevo nombre de producto con la palabra Copia
      $nombre          = sprintf('%s - Copia', $producto['nombre']);

      // Generar un nuevo sku si no tiene
      $sku             = random_password(10, 'numeric');
      $producto['sku'] = $sku;

      $data =
      [
        'imagen'      => $producto['imagen'],
        'nombre'      => $nombre,
        'descripcion' => $producto['descripcion'],
        'sku'         => $producto['sku'],
        'corte'       => $producto['corte'],
        'variantes'   => $producto['variantes'],
        'adjuntos'    => $producto['adjuntos'],
        'precio'      => $producto['precio'],
        'creado'      => now()
      ];

      // Guardar el registro en la db
      if (!$nuevo_producto = productoModel::add('productos', $data)) {
        throw new PDOException('Hubo un problema al duplicar el producto.');
      }

      Flasher::new(sprintf('Producto <b>%s</b> duplicado con éxito.', $nombre), 'success');
      Redirect::to('productos');

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }
}

