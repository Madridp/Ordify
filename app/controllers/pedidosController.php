<?php
use Verot\Upload\Upload;

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de pedidos
 */
class pedidosController extends Controller {
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
      'title'   => 'Pedidos',
      'pedidos' => pedidoModel::all_paginated()
    ];
    
    View::render('index', $data);
  }

  function ver($id)
  {
    if (!$p = pedidoModel::by_id($id)) {
      Flasher::new('El pedido no existe en la base de datos.', 'danger');
      Redirect::back();
    }

    $data =
    [
      'title'    => sprintf('Pedido #%s', $p['numero']),
      'slug'     => 'pedidos',
      'p'        => $p,
      'opciones' => get_select_productos()
    ];

    View::render('ver', $data);
  }

  function agregar()
  {
    $data =
    [
      'title'       => 'Agregar pedido',
      'slug'        => 'pedidos',
      'proveedores' => proveedorModel::all()
    ];

    View::render('agregar', $data);
  }

  function post_agregar()
  {
    try {
      if (!check_posted_data(['id_proveedor', 'fecha_entrega', 'notas', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        Flasher::deny();
        Redirect::back();
      }

      $id_proveedor  = clean($_POST['id_proveedor']);
      $fecha_entrega = clean($_POST['fecha_entrega']);
      $notas         = clean($_POST['notas']);
      
      // Validar que el proveedor existe
      if (!$proveedor = proveedorModel::by_id($id_proveedor)) {
        throw new PDOException('No existe el proveedor seleccionado en la base de datos.');
      }

      // Guardar el nuevo registro en la base de datos
      $data =
      [
        'numero'        => rand(111111, 999999),
        'id_proveedor'  => $id_proveedor,
        'notas'         => $notas,
        'subtotal'      => 0,
        'total'         => 0,
        'status'        => 'borrador',
        'fecha_entrega' => $fecha_entrega,
        'hash'          => generate_token(),
        'metodo_pago'   => 'spei',
        'status_pago'   => 'pendiente',
        'fecha_pago'    => null,
        'total_pagado'  => 0,
        'creado'        => now()
      ];

      if (!$id = pedidoModel::add(pedidoModel::$t1, $data)) {
        throw new PDOException('Hubo un problema al agregar el pedido.');
      }

      $pedido = pedidoModel::by_id($id);

      Flasher::new(sprintf('Pedido <b>%s</b> iniciado con éxito.', $pedido['numero']), 'success');
      Redirect::to(sprintf('pedidos/ver/%s', $pedido['id']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function realizar($id)
  {
    try {
      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada para el usuario.');
      }
      
      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) {
        throw new PDOException('No existe el proveedor del pedido.');
      }

      // Validar el status actual del pedido
      // solo un pedido en borrador puede ser enviado al Proveedor
      if (!in_array($pedido['status'], ['borrador'])) {
        throw new PDOException('El estado actual del pedido no es válido para actualizar.');
      }

      // Validar que exista por lo menos un item en el producto
      if (empty($pedido['productos'])) {
        throw new PDOException('No hay productos en el pedido actual, debe contener por lo menos uno.');
      }

      // Guardar el nuevo estado del pedido
      if (!pedidoModel::update(pedidoModel::$t1, ['id' => $id], ['status' => 'pendiente', 'fecha_inicio' => now()])) {
        throw new PDOException('Hubo un problema al actualizar el estado del pedido.');
      }

      // Enviar email al proveedor
      send_email_nuevo_pedido($id);

      // Redirigir
      Flasher::new(sprintf('Pedido <b>%s</b> realizado con éxito.', $pedido['numero']), 'success');
      Flasher::new(sprintf('Hemos informado a <b>%s</b> por correo electrónico <b>(%s)</b> del nuevo pedido.', $proveedor['razon_social'], $proveedor['email']));
      Redirect::to(sprintf('pedidos/ver/%s', $pedido['id']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function en_camino($id)
  {
    try {
      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) {
        throw new PDOException('No existe el proveedor del pedido.');
      }

      // Validar el status actual del pedido
      // solo un pedido en borrador puede ser enviado al Proveedor
      if (!in_array($pedido['status'], ['pendiente'])) {
        throw new PDOException('El estado actual del pedido no es válido para actualizar.');
      }

      // Validar que exista por lo menos un item en el producto
      if (empty($pedido['productos'])) {
        throw new PDOException('No hay productos en el pedido actual, debe contener por lo menos uno.');
      }

      // Guardar el nuevo estado del pedido
      if (!pedidoModel::update(pedidoModel::$t1, ['id' => $id], ['status' => 'en_camino'])) {
        throw new PDOException('Hubo un problema al actualizar el estado del pedido.');
      }

      // Redirigir
      Flasher::new(sprintf('¡El pedido <b>%s</b> está en camino!.', $pedido['numero']), 'success');
      Redirect::to(sprintf('pedidos/ver/%s', $pedido['id']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function recibir($id)
  {
    try {
      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) {
        throw new PDOException('No existe el proveedor del pedido.');
      }

      // Validar el status actual del pedido
      // solo un pedido en en_camino puede ser recibido
      if (!in_array($pedido['status'], ['en_camino'])) {
        throw new PDOException('El estado actual del pedido no es válido para actualizar.');
      }

      // Validar que exista por lo menos un item en el producto
      if (empty($pedido['productos'])) {
        throw new PDOException('No hay productos en el pedido actual, debe contener por lo menos uno.');
      }

      // Guardar el nuevo estado del pedido
      if (!pedidoModel::update(pedidoModel::$t1, ['id' => $id], ['status' => 'recibido', 'fecha_entrega' => now()])) {
        throw new PDOException('Hubo un problema al actualizar el estado del pedido.');
      }

      // Redirigir
      Flasher::new(sprintf('¡El pedido <b>%s</b> fue recibido con éxito!', $pedido['numero']), 'success');
      Redirect::to(sprintf('pedidos/ver/%s', $pedido['id']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function procesar($id)
  {
    try {
      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      if (!is_admin()) {
        throw new Exception('Acción no autorizada.');
      }

      if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) {
        throw new PDOException('No existe el proveedor del pedido.');
      }

      // Validar el status actual del pedido
      // solo un pedido en recibido puede ser procesado
      if (!in_array($pedido['status'], ['recibido'])) {
        throw new PDOException('El estado actual del pedido no es válido para actualizar.');
      }

      // Validar que exista por lo menos un item en el producto
      if (empty($pedido['productos'])) {
        throw new PDOException('No hay productos en el pedido actual, debe contener por lo menos uno.');
      }

      // Guardar el nuevo estado del pedido
      if (!pedidoModel::update(pedidoModel::$t1, ['id' => $id], ['status' => 'procesando'])) {
        throw new PDOException('Hubo un problema al actualizar el estado del pedido.');
      }

      // Redirigir
      Flasher::new(sprintf('Procesando <b>%s</b> unidades del pedido <b>%s</b>.', $pedido['total_cantidades'], $pedido['numero']), 'success');
      Redirect::to(sprintf('pedidos/ver/%s', $pedido['id']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function completar($id)
  {
    try {
      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new Exception('Acción no autorizada para el usuario.');
      }

      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) {
        throw new PDOException('No existe el proveedor del pedido.');
      }

      // Validar el status actual del pedido
      // solo un pedido en procesando puede ser completado
      if (!in_array($pedido['status'], ['procesando'])) {
        throw new PDOException('El estado actual del pedido no es válido para actualizar.');
      }
      
      // Validar que exista por lo menos un item en el producto
      if ((int) $pedido['total_procesados'] === 0) {
        throw new PDOException('No puedes completar este pedido, aún no hay unidades recibidas.');
      }
      
      // Guardar el nuevo estado del pedido
      if (!pedidoModel::update(pedidoModel::$t1, ['id' => $id], ['status' => 'completado'])) {
        throw new PDOException('Hubo un problema al actualizar el estado del pedido.');
      }

      // Resumen del pedido
      $pedido     = pedidoModel::by_id($id);
      $total      = $pedido['total_cantidades'];
      $danadas    = $pedido['total_rechazados'];
      $recibidas  = $pedido['total_recibidos'];
      $procesados = $pedido['total_procesados'];
      $faltantes  = $total - $procesados;
      Flasher::new(sprintf('¡Pedido <b>%s</b> completado con éxito!', $pedido['numero']), 'success');
      Flasher::new(sprintf('Se recibieron <b>%s</b> de <b>%s</b> unidades.', $procesados, $total));

      // Unidades extras
      if ($procesados > $total) {
        Flasher::new(sprintf('Hubo <b>%s</b> unidades extras en el pedido.', $procesados - $total), 'info');
      }
      
      // Unidades dañadas
      if ($danadas > 0) {
        Flasher::new(sprintf('Hubo <b>%s</b> unidades dañadas en el pedido.', $danadas), 'danger');
      }
      
      // Unidades faltantes
      if ($faltantes > 0) {
        Flasher::new(sprintf('Hubo <b>%s</b> unidades faltantes en el pedido.', $faltantes), 'danger');
      } else if ($faltantes == 0) {
        Flasher::new('¡No hubo unidades faltantes en el pedido!', 'success');
      }
      
      // Redirigir
      Redirect::to(sprintf('pedidos/ver/%s', $pedido['id']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to(sprintf('pedidos/ver/%s', $id));
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to(sprintf('pedidos/ver/%s', $id));
    }
  }

  function cancelar($id)
  {
    try {
      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new Exception('Acción no autorizada para el usuario.');
      }

      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) {
        throw new PDOException('No existe el proveedor del pedido.');
      }

      // Validar el status actual del pedido
      // solo un pedido en borrador puede ser enviado al Proveedor
      if (!in_array($pedido['status'], ['pendiente'])) {
        throw new PDOException('El estado actual del pedido no es válido para actualizar.');
      }

      // Validar que exista por lo menos un item en el producto
      if (empty($pedido['productos'])) {
        throw new PDOException('No hay productos en el pedido actual, debe contener por lo menos uno.');
      }

      // Guardar el nuevo estado del pedido
      $data = 
      [
        'status'       => 'cancelado',
        'status_pago'  => 'cancelado',
        'total_pagado' => 0,
        'fecha_pago'   => null
      ];

      if (!pedidoModel::update(pedidoModel::$t1, ['id' => $id], $data)) {
        throw new PDOException('Hubo un problema al actualizar el estado del pedido.');
      }

      // Enviar email al proveedor
      $email   = $proveedor['email'];
      $subject = sprintf('[%s] - Pedido %s cancelado', get_sitename(), $pedido['numero']);
      $body    = sprintf('El pedido #%s anteriormente generado y solicitado ha sido cancelado.', $pedido['numero']);
      $alt     = 'Pedido cancelado, clic para ver más información.';
      send_email(get_siteemail(), $email, $subject, $body, $alt);

      // Redirigir
      Flasher::new(sprintf('El pedido <b>%s</b> fue cancelado con éxito.', $pedido['numero']), 'success');
      Flasher::new(sprintf('Hemos informado a <b>%s</b> por correo electrónico <b>(%s)</b> de la cancelación del pedido.', $proveedor['razon_social'], $proveedor['email']));
      Redirect::to(sprintf('pedidos/ver/%s', $pedido['id']));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function borrar($id)
  {
    try {
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])) {
        Flasher::deny();
        Redirect::back();
      }

      // Para forzar el borrado de un registro como administrador
      $forzar_borrado = isset($_GET["force_delete"]) ? true : false;

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new Exception('Acción no autorizada para el usuario.');
      }

      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      // Validar el status actual del pedido
      // solo un pedido en borrador puede ser enviado al Proveedor
      if (!in_array($pedido['status'], ['borrador', 'pendiente', 'cancelado']) && $forzar_borrado == false) {
        throw new PDOException('El estado actual del pedido no es válido para borrarlo.');
      }

      // Borrar el pedido de la base de datos
      if (pedidoModel::remove_by_id($id) === false) {
        throw new PDOException('Hubo un problema al borrar el pedido.');
      }

      // Redirigir
      Flasher::new(sprintf('El pedido <b>#%s</b> fue borrado con éxito.', $pedido['numero']), 'success');
      Redirect::to('pedidos');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function export($id)
  {
    try {
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new Exception('El pedido no existe en la base de datos.');
      }

      $productos = $pedido['productos'];

      if (empty($productos)) {
        throw new Exception('No hay productos en el pedido para exportar.');
      }

      $filename   = sprintf('Exported_csv_%s.csv', $pedido['numero']);

      // Descargando
      header('Content-Encoding: UTF-8');
      header('Content-type: text/csv; charset=UTF-8');
      header('Content-Disposition: attachment; filename="'.$filename.'"');
      echo "\xEF\xBB\xBF"; // UTF-8 BOM
      
      // Insertando la información en el csv
      $fp = fopen('php://output', 'wb');

      // Cabeceras
      fputcsv($fp, ['Producto', 'Corte', 'Cantidad', 'Precio', 'Subtotal', 'Total']);

      // Filas
      foreach ( $productos as $p ) {
        $row =
        [
          $p['nombre'],
          $p['corte'],
          $p['cantidad'],
          money($p['precio']),
          money($p['subtotal']),
          money($p['total'])
        ];
        fputcsv($fp, $row);
      }

      // Espaciado
      fputcsv($fp, []);

      // Resumen del pedido
      fputcsv($fp, [sprintf('Resumen pedido #%s', $pedido['numero']), 'Piezas Totales', $pedido['total_cantidades'], '', 'Total', money($pedido['total'])]);

      // Espaciado
      fputcsv($fp, []);

      // Notas
      fputcsv($fp, ['Notas']);
      fputcsv($fp, [$pedido['notas']]);

      fclose($fp);
      return true;

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function duplicar($id)
  {
    try {
      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new Exception('Acción no autorizada para el usuario.');
      }

      if (!$pedido = pedidoModel::by_id($id)) {
        Flasher::new('El pedido no existe en la base de datos.', 'danger');
        Redirect::back();
      }

      if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) {
        throw new PDOException('No existe el proveedor del pedido.');
      }

      // Crear el array de información del nuevo pedido con base al actual a duplicar
      $data =
      [
        'numero'        => rand(111111, 999999),
        'id_proveedor'  => $pedido['id_proveedor'],
        'notas'         => null,
        'subtotal'      => $pedido['subtotal'],
        'total'         => $pedido['total'],
        'status'        => 'borrador',
        'fecha_entrega' => $pedido['fecha_entrega'],
        'hash'          => generate_token(),
        'metodo_pago'   => 'spei',
        'status_pago'   => 'pendiente',
        'fecha_pago'    => null,
        'total_pagado'  => 0,
        'creado'        => now()
      ];

      if (!$id = pedidoModel::add(pedidoModel::$t1, $data)) {
        throw new PDOException('Hubo un problema al duplicar el pedido.');
      }

      // Notificación al usuario
      Flasher::new(sprintf('Pedido <b>#%s</b> duplicado con éxito al <b>#%s</b>.', $pedido['numero'], $data['numero']), 'success');

      // Agregar los productos al pedido duplicado solo en caso de que tenga productos agregados
      $errores = 0;
      if (!empty($pedido['productos'])) {
        foreach ($pedido['productos'] as $producto) {

          // Registrar el producto en el pedido
          $data     =
          [
            'id_pedido'   => $id,
            'id_producto' => $producto['id_producto'],
            'nombre'      => $producto['nombre'],
            'corte'       => $producto['corte'],
            'cantidad'    => $producto['cantidad'],
            'precio'      => $producto['precio'],
            'subtotal'    => $producto['subtotal'],
            'total'       => $producto['total'],
            'recibidos'   => 0,
            'danados'     => 0,
            'cancelados'  => 0
          ];

          if (!$row_id = pedidoModel::add(pedidoModel::$t2, $data)) {
            $errores++;
          }

        }

        // Validar si hubo errores
        if ($errores > 0) {
          Flasher::new(sprintf('Hubo <b>%s</b> errores al duplicar los productos del pedido <b>#%s</b>', $errores, $pedido['numero']));
        }
      }
      
      // Redirigir
      Redirect::to(sprintf('pedidos/ver/%s', $id));

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to('pedidos');
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to('pedidos');
    }
  }

  function post_adjuntos()
  {
    $id        = isset($_POST["id"]) ? clean($_POST["id"]) : null;
    $redirect  = sprintf('pedidos/ver/%s', $id);

    try {
      if (!Csrf::validate($_POST['csrf']) || $id === null) {
        Flasher::deny();
        Redirect::to($redirect);
      }

      // Validación de información ingresada
      $imagenes = $_FILES["imagenes"];
      $imagenes = arrenge_posted_files($imagenes);
      $errors   = 0;
      $success  = 0;

      // Validar que exista el pedido en curso
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new Exception('No existe el pedido en la base de datos.');
      }

      // Validar que el status del pedido no sea completado o similar
      if (in_array($pedido['status'], ['completado','cancelado']) && !is_admin()) {
        throw new Exception('Ya no es posible añadir adjuntos al pedido actual.');
      }

      foreach ($imagenes as $img) {
        if ($img['error'] == 4) {
          $errors++;
          continue;
        }

        // Inicio de procesamiento de imagen
        $handler = new Upload($img);
        
        if (!$handler->uploaded) {
          $errors++;
          continue;
        }

        $handler->file_new_name_body = generate_filename();
        $handler->image_resize       = true;
        $handler->image_ratio_y      = true;
        $handler->image_x            = 700;
  
        // Mover al directorio
        $handler->process(UPLOADS);
        if (!$handler->processed) {
          Flasher::new($handler->error, 'danger');
          $errors++;
          continue;
        }

        $imagen = $handler->file_dst_name; // nombre de la imagen guardada en el servidor
        $handler->clean();
  
        $post =
        [
          'tipo'         => 'adjunto',
          'id_padre'     => 0,
          'id_usuario'   => get_user('id'),
          'id_ref'       => $id,
          'titulo'       => 'Adjunto de pedido',
          'permalink'    => UPLOADED.$imagen,
          'contenido'    => $imagen,
          'status'       => 'public',
          'mime_type'    => null,
          'deadline_at'  => now(),
          'completed_at' => now(),
          'created_at'   => now(),
          'updated_at'   => now()
        ];
  
        // Guardar el registro en la db
        if (!$id_post = postModel::add('posts', $post)) {
          $errors++;
          unlink(UPLOADS.$imagen);
          continue;
        }

        $success++;
      }

      if ($errors > 0) {
        Flasher::new(sprintf('Hubo <b>%s</b> errores encontrados.', $errors), 'danger');
      }

      if ($success > 0) {
        Flasher::new(sprintf('Se procesaron <b>%s</b> imágenes con éxito.', $success), 'success');
      }

      Redirect::to(sprintf('pedidos/ver/%s', $id));

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to($redirect);
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to($redirect);
    }
  }

  function borrar_adjunto($id_post)
  {
    $id_pedido = isset($_GET["id_pedido"]) ? clean($_GET["id_pedido"]) : null;
    $redirect  = sprintf('pedidos/ver/%s', $id_pedido);
    
    try {
      if (!Csrf::validate($_GET["_t"])) {
        Flasher::deny();
        Redirect::to($redirect);
      }

      if (!is_admin()) {
        throw new Exception('No tienes permisos para realizar esta acción.');
      }

      // Validación de información ingresada
      $id_post   = clean($id_post);

      // Validar que exista el pedido
      if (!$pedido = pedidoModel::by_id($id_pedido)) {
        throw new Exception('No existe el pedido en la base de datos.');
      }

      // Validar que el status del pedido no sea completado o similar
      if (in_array($pedido['status'], ['completado','cancelado']) && !is_admin()) {
        throw new Exception('Ya no es posible borrar adjuntos del pedido actual.');
      }

      // Verificar que exista el post en la db
      if (!$post = postModel::by_id($id_post)) {
        throw new Exception('No existe el registro en la base de datos.');
      }

      if ($post['tipo'] !== 'adjunto') {
        throw new Exception('El registro no es un adjunto válido.');
      }

      // Borrar el registro
      postModel::remove(postModel::$t1, ['id' => $id_post]);

      // Verificar que exista la imagen y borrarla del server
      if (is_file(UPLOADS.$post['contenido'])) {
        $ok = unlink(UPLOADS.$post['contenido']);

        if ($ok === true) {
          Flasher::new(sprintf('El adjunto <b>%s</b> ha sido borrado con éxito.', $post['contenido']), 'success');
        }
      } else {
        Flasher::new('El archivo ya no existia en el servidor, pero se ha borrado el registro correctamente.', 'success');
      }

      Redirect::to($redirect);

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to($redirect);
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to($redirect);
    }
  }
}

