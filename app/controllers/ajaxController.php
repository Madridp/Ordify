<?php 

use Verot\Upload\Upload;

class ajaxController extends Controller {
  
  private $accepted_actions = ['add', 'get', 'load', 'update', 'delete', 'post', 'put', 'delete'];
  private $required_params  = ['hook', 'action'];

  function __construct()
  {
    foreach ($this->required_params as $param) {
      if(!isset($_POST[$param])) {
        json_output(json_build(403));
      }
    }

    if(!in_array($_POST['action'], $this->accepted_actions)) {
      json_output(json_build(403));
    }
  }

  function index()
  {
    /**
    200 OK
    201 Created
    300 Multiple Choices
    301 Moved Permanently
    302 Found
    304 Not Modified
    307 Temporary Redirect
    400 Bad Request
    401 Unauthorized
    403 Forbidden
    404 Not Found
    410 Gone
    500 Internal Server Error
    501 Not Implemented
    503 Service Unavailable
    550 Permission denied
    */
    json_output(json_build(403));
  }

  function bee_add_movement()
  {
    try {
      $mov              = new movementModel();
      $mov->type        = $_POST['type'];
      $mov->description = $_POST['description'];
      $mov->amount      = (float) $_POST['amount'];
      if(!$id = $mov->add()) {
        json_output(json_build(400, null, 'Hubo error al guardar el registro'));
      }
  
      // se guardó con éxito
      $mov->id = $id;
      json_output(json_build(201, $mov->one(), 'Movimiento agregado con éxito'));
      
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function bee_get_movements()
  {
    try {
      $movements          = new movementModel;
      $movs               = $movements->all_by_date();

      $taxes              = (float) get_option('taxes') < 0 ? 16 : get_option('taxes');
      $use_taxes          = get_option('use_taxes') === 'Si' ? true : false;
      
      $total_movements    = $movs[0]['total'];
      $total              = $movs[0]['total_incomes'] - $movs[0]['total_expenses'];
      $subtotal           = $use_taxes ? $total / (1.0 + ($taxes / 100)) : $total;
      $taxes              = $subtotal * ($taxes / 100);
      
      $calculations       = [
        'total_movements' => $total_movements,
        'subtotal'        => $subtotal,
        'taxes'           => $taxes,
        'total'           => $total
      ];

      $data = get_module('movements', ['movements' => $movs, 'cal' => $calculations]);
      json_output(json_build(200, $data));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }

  }

  function bee_delete_movement()
  {
    try {
      $mov     = new movementModel();
      $mov->id = $_POST['id'];

      if(!$mov->delete()) {
        json_output(json_build(400, null, 'Hubo error al borrar el registro'));
      }

      json_output(json_build(200, null, 'Movimiento borrado con éxito'));
      
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function bee_update_movement()
  {
    try {
      $movement     = new movementModel;
      $movement->id = $_POST['id'];
      $mov          = $movement->one();

      if(!$mov) {
        json_output(json_build(400, null, 'No existe el movimiento'));
      }

      $data = get_module('updateForm', $mov);
      json_output(json_build(200, $data));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function bee_save_movement()
  {
    try {
      $mov              = new movementModel();
      $mov->id          = $_POST['id'];
      $mov->type        = $_POST['type'];
      $mov->description = $_POST['description'];
      $mov->amount      = (float) $_POST['amount'];
      if(!$mov->update()) {
        json_output(json_build(400, null, 'Hubo error al guardar los cambios'));
      }
  
      // se guardó con éxito
      json_output(json_build(200, $mov->one(), 'Movimiento actualizado con éxito'));
      
    } catch (Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function bee_save_options()
  {
    $options =
    [
      'use_taxes' => $_POST['use_taxes'],
      'taxes'     => (float) $_POST['taxes'],
      'coin'      => $_POST['coin']
    ];

    foreach ($options as $k => $option) {
      try {
        if(!$id = optionModel::save($k, $option)) {
          json_output(json_build(400, null, sprintf('Hubo error al guardar la opción %s', $k)));
        }
    
        
      } catch (Exception $e) {
        json_output(json_build(400, null, $e->getMessage()));
      }
    }

    // se guardó con éxito
    json_output(json_build(200, null, 'Opciones actualizadas con éxito'));
  }

  /**
   * Función para cargar la información que
   * se utilizará para dibujar la gráfica del dashboard en cuanto
   * pedidos realizados por mes
   *
   */
  function chart_pedidos()
  {
    try {
      $meses = isset($_POST["meses"]) ? clean($_POST["meses"]) : 12;
      $data  = pedidoModel::total_by_year($meses);
      json_output(json_build(200, $data));
    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para cargar data y dibujar gráfica
   * de inversión anual en pedidos a proveedores
   * Ordify
   *
   */
  function chart_inversion()
  {
    try {
      json_output(json_build(200, pedidoModel::total_inversion_by_year(isset($_POST['year']) ? $_POST['year'] : date('Y'))));
    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Utilizada para buscar coincidencias en el campo de búsqueda
   * superior del sitio Ordify
   * se pueden implementar más opciones de busqueda
   * actualmente solo regresan 3 tipos de resultados
   * productos, pedidos y proveedores
   *
   */
  function serch_terms()
  {
    try {
      if (!check_posted_data(['term'], $_POST)) {
        throw new Exception('Ingresa un término para buscar válido.');
      }

      $term       = clean($_POST['term']);
      $serch_term = sprintf('%%%s%%', $term);

      // Buscar resultados en la base de datos
      $data                = [];
      $data['term']        = $term;
      $data['serch_term']  = $serch_term;
      // Pedidos
      $sql                 = 'SELECT DISTINCT p.*, pr.nombre, pr.razon_social FROM pedidos p LEFT JOIN proveedores pr ON pr.id = p.id_proveedor WHERE p.numero LIKE :place ORDER BY p.id DESC';
      $data['pedidos']     = ($rows = pedidoModel::query($sql, ['place' => $serch_term])) ? $rows : [];

      // Proveedores
      $sql                 = 'SELECT DISTINCT p.* FROM proveedores p WHERE p.razon_social LIKE :razon OR p.nombre LIKE :nombre OR p.email LIKE :email ORDER BY p.id DESC';
      $data['proveedores'] = ($rows = proveedorModel::query($sql, ['razon' => $serch_term, 'nombre' => $serch_term, 'email' => $serch_term])) ? $rows : [];

      // Productos
      $sql                 = 'SELECT DISTINCT p.* FROM productos p WHERE p.nombre LIKE :nombre OR p.sku LIKE :sku OR p.descripcion LIKE :desc ORDER BY p.id DESC';
      $data['productos']   = ($rows = proveedorModel::query($sql, ['nombre' => $serch_term, 'sku' => $serch_term, 'desc' => $serch_term])) ? $rows : [];

      // Productos
      $html = get_module('serchResults', $data);

      json_output(json_build(200, $html));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function do_buscar_productos()
  {
    try {
      if (!check_posted_data(['termino'], $_POST)) {
        throw new Exception('Ingresa un término para buscar válido.');
      }

      $term = clean($_POST['termino']);
      $res  = productoModel::serch($term);
      $data = [];

      if (empty($res)) {
        throw new Exception(sprintf('No hay resultados para la busqueda "%s".', $term));
      }

      // Formatear
      // Iterar sobre los productos y extraer cada variante posible
      foreach ($res as $p) {
        // El producto no tiene variantes disponibles
        if (empty($p['variantes']) || $p['variantes'] == '') {
          $producto = 
          [
            'id'       => $p['id'],
            'nombre'   => $p['nombre'],
            'precio'   => $p['precio'],
            'sku'      => $p['sku'],
            'corte'    => $p['corte'],
            'variante' => null
          ];
          
          $data[] = $producto;
          continue;
        }

        // El producto si tiene variantes
        $variantes = explode('|', $p['variantes']);

        // Iterar sobre cada variante para generar el producto correspondiente
        // Esto puede hacerse de mejor manera y sperar las variantes del producto principal
        // Pero de esta forma será directa la integración del registro en el pedido
        foreach ($variantes as $v) {
          $producto = 
          [
            'id'       => $p['id'],
            'nombre'   => sprintf('%s - %s', $p['nombre'], $v),
            'precio'   => $p['precio'],
            'sku'      => $p['sku'],
            'corte'    => $p['corte'],
            'variante' => $v
          ];

          $data[] = $producto;
          continue;
        }
      }

      json_output(json_build(200, $data));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para cargar el módulo al ver un pedido
   * y la información de un pedido en partícular
   * dependiendo del id recabado
   *
   */
  function do_get_pedido()
  {
    try {
      if (!check_posted_data(['id', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id = clean($_POST['id']);
      
      // Validar que exista el registro del pedido
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      json_output(json_build(200, $pedido));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para cargar el resumen de un pedido
   *
   * @return void
   */
  function do_get_data_pedido()
  {
    try {
      if (!check_posted_data(['id', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id = clean($_POST['id']);
      
      // Validar que exista el registro del pedido
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      // Formatear el modulo a regresar a la vista
      $data         = $pedido;
      $data['html'] = get_module('pedidos/dataPedido', $data);

      json_output(json_build(200, $data));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para cargar y formatear la información del proveedor de un solo pedido
   *
   * @return void
   */
  function do_get_data_proveedor()
  {
    try {
      if (!check_posted_data(['id', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id = clean($_POST['id']);
      
      // Validar que exista el registro del pedido
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      // Formatear el modulo a regresar a la vista
      $data                = $pedido;
      $data['proveedores'] = proveedorModel::all();
      $data['html']        = get_module('pedidos/dataProveedor', $data);

      json_output(json_build(200, $data));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para cargar todos los productos y la tabla de productos en página de 1 solo pedido
   *
   * @return void
   */
  function do_get_data_productos()
  {
    try {
      if (!check_posted_data(['id', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id = clean($_POST['id']);
      
      // Validar que exista el registro del pedido
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      // Formatear el modulo a regresar a la vista
      $data                = $pedido;
      $data['html']        = get_module('pedidos/dataProductos', $data);

      json_output(json_build(200, $data));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para agregar un producto al 
   * pedido en curso
   *
   */
  function do_add_producto_a_pedido()
  {
    try {
      if (!check_posted_data(['id', 'csrf', 'nombre', 'id_pedido', 'variante'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id        = clean($_POST['id']);
      $id_pedido = clean($_POST['id_pedido']);
      $nombre    = clean($_POST['nombre']);
      $variante  = clean($_POST['variante']);
      $extra     = in_array($variante, ['XL','xl','+20']);

      // Validar que exista el pedido en la base de datos
      if (!$pedido = pedidoModel::by_id($id_pedido)) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      // Validar status del pedido
      if (!in_array($pedido['status'], ['borrador'])) {
        throw new PDOException('Ya no puedes agregar más productos a este pedido.');
      }
      
      // Validar que exista el registro del producto en la base de datos
      if (!$producto = productoModel::by_id($id)) {
        throw new PDOException('No existe el producto en la base de datos.');
      }

      // Validar si el producto ya se encuentra en existencia en el pedido
      // si se encuentra deberá sumarse 1 unidad
      $sql = 'SELECT pp.* FROM pedidos_productos pp WHERE pp.nombre = :nombre AND pp.id_producto = :id AND pp.id_pedido = :id_pedido LIMIT 1';
      if ($rows = pedidoModel::query($sql, ['nombre' => $nombre, 'id' => $id, 'id_pedido' => $id_pedido])) {
        
        $row      = $rows[0];
        $cantidad = $row['cantidad'] + 1;
        $precio   = $producto['precio'] + ($extra ? 20 : 0);
        $total    = $cantidad * $precio;
        $subtotal = $total / get_taxes_rate();
        $data     =
        [
          'variante' => $variante,
          'precio'   => $precio,
          'cantidad' => $cantidad,
          'subtotal' => $subtotal,
          'total'    => $total
        ];

        pedidoModel::update(pedidoModel::$t2, ['id' => $row['id']], $data);

        // Recalcular el pedido
        pedidoModel::recalculate_by_id($id_pedido);

        // Respuesta
        json_output(json_build(200, $producto, 'Producto actualizado con éxito.'));
      }

      // Registrar el nuevo producto en el pedido si no existe en la base de datos
      $precio   = (float) $producto['precio'] + ($extra ? 20 : 0);
      $total    = (float) $precio * 1;
      $subtotal = (float) $total / get_taxes_rate();
      $data     =
      [
        'id_pedido'   => $id_pedido,
        'id_producto' => $id,
        'nombre'      => $nombre,
        'variante'    => $variante,
        'corte'       => $producto['corte'],
        'cantidad'    => 1,
        'precio'      => $precio,
        'subtotal'    => $subtotal,
        'total'       => $total,
        'recibidos'   => 0,
        'danados'     => 0,
        'cancelados'  => 0
      ];
      if (!$row_id = pedidoModel::add(pedidoModel::$t2, $data)) {
        throw new PDOException('Hubo un problema al agregar el registro.');
      }

      // Recalcular los elementos del pedido
      pedidoModel::recalculate_by_id($id_pedido);

      // Regresar la respuesta
      json_output(json_build(201, $producto, 'Producto agregado con éxito al pedido.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para editar las cantidades de algún producto en un pedido
   *
   */
  function do_update_cantidad_producto_pedido()
  {
    try {
      if (!check_posted_data(['id', 'csrf', 'cantidad'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id       = clean($_POST['id']); // id del registro en la tabla pedidos_productos
      $cantidad = clean($_POST['cantidad']);

      // Validar que exista al item en efecto
      if (!$item = pedidoModel::item_by_id($id)) {
        json_output(json_build(400, null, 'No existe el registro del producto en el pedido.'));
      }

      // Validar que exista el pedido en la base de datos
      if (!$pedido = pedidoModel::by_id($item['id_pedido'])) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      // Validar status del pedido
      if (!in_array($pedido['status'], ['borrador'])) {
        throw new PDOException('Ya no puedes actualizar las cantidades de este pedido.');
      }

      // Cargando data del producto
      if (!$producto = productoModel::by_id($item['id_producto'])) {
        json_output(json_build(400, null, 'No existe el producto en la base de datos.'));
      }

      // Actualizar el registro de la cantidad ingresada
      $variante = $item['variante'];
      $extra    = in_array($variante, ['XL','xl','+20']);
      $precio   = $producto['precio'] + ($extra ? 20 : 0);
      $total    = (float) $cantidad * $precio;
      $subtotal = (float) $total / get_taxes_rate();

      $data =
      [
        'cantidad' => $cantidad,
        'precio'   => $precio,
        'total'    => $total,
        'subtotal' => $subtotal
      ];

      pedidoModel::update(pedidoModel::$t2, ['id' => $id], $data);

      // Recalcular los elementos del pedido
      pedidoModel::recalculate_by_id($item['id_pedido']);

      // Regresar la respuesta
      json_output(json_build(200, $producto, 'Cantidades actualizadas.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

    /**
   * Función para editar las cantidades de algún producto en un pedido
   *
   */
  function do_update_recibidos()
  {
    try {
      if (!check_posted_data(['id', 'csrf', 'cantidad', 'tipo'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id              = clean($_POST['id']); // id del registro en la tabla pedidos_productos
      $cantidad        = clean($_POST['cantidad']);
      $cantidad        = empty($cantidad) ? 0 : (int) $cantidad;
      $tipo_movimiento = clean($_POST["tipo"]); // tipo de movimiento a registrar

      // Validar que exista al item en efecto
      if (!$item = pedidoModel::item_by_id($id)) {
        json_output(json_build(400, null, 'No existe el registro del producto en el pedido.'));
      }

      // Validar que exista el pedido en la base de datos
      if (!$pedido = pedidoModel::by_id($item['id_pedido'])) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      // Validar status del pedido
      if (!in_array($pedido['status'], ['procesando'])) {
        throw new PDOException('No puedes actualizar las cantidades recibidas de este pedido.');
      }

      // Cargando data del producto
      if (!$producto = productoModel::by_id($item['id_producto'])) {
        throw new PDOException('No existe el producto en la base de datos.');
      }

      // Almacenar de forma local las cantidades
      $requeridos = $item['cantidad'];
      $recibidos  = $item['recibidos'];
      $danados    = $item['danados'];
      $cancelados = $item['cancelados'];
      $sum        = $cantidad < 0 ? false : true;
      $cantidad   = abs($cantidad); // valor absoluto, sin el símbolo "-"

      // Actualizar el registro de la cantidad ingresada
      switch ($tipo_movimiento) {
        case 'damaged':
          $danados = $sum ? $danados + $cantidad : $danados - $cantidad;
          pedidoModel::update(pedidoModel::$t2, ['id' => $id], ['danados' => $danados]);
          break;
        
        case 'canceled':
          $cancelados = $sum ? $cancelados + $cantidad : $cancelados - $cantidad;
          pedidoModel::update(pedidoModel::$t2, ['id' => $id], ['cancelados' => $cancelados]);
          break;
            
        case 'incoming':
          $recibidos = $sum ? $recibidos + $cantidad : $recibidos - $cantidad;
          pedidoModel::update(pedidoModel::$t2, ['id' => $id], ['recibidos' => $recibidos]);
          break;

        default:
          throw new Exception('Hubo un error en la actualización de inventario.');
          break;
      }

      // Recalcular los elementos del pedido
      pedidoModel::recalculate_by_id($item['id_pedido']);

      // Regresar la respuesta
      json_output(json_build(200, $producto, 'Productos recibidos actualizados.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para borrar un item o producto de un pedido
   * en progreso
   *
   */
  function do_delete_producto_de_pedido()
  {
    try {
      if (!check_posted_data(['id', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id = clean($_POST['id']); // id del registro en la tabla pedidos_productos

      // Validar que exista al item en efecto
      if (!$item = pedidoModel::item_by_id($id)) {
        json_output(json_build(400, null, 'No existe el registro del producto en el pedido.'));
      }

      // Validar que exista el pedido en la base de datos
      if (!$pedido = pedidoModel::by_id($item['id_pedido'])) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      // Validar status del pedido
      if (!in_array($pedido['status'], ['borrador'])) {
        throw new PDOException('Ya no puedes borrar productos de este pedido.');
      }

      // Cargando data del producto
      if (!$producto = productoModel::by_id($item['id_producto'])) {
        pedidoModel::remove(pedidoModel::$t2, ['id' => $id, 'id_producto' => $item['id_producto']]); // Solo por protección, borrar todos los elementos que se encontraron en la base de datos de pedidos_productos
        pedidoModel::recalculate_by_id($item['id_pedido']); // recalcular para actualizar los elementos
        json_output(json_build(400, null, 'No existe el producto en la base de datos, pedido actualizado.'));
      }

      // Borrar el registro de la base de datos
      pedidoModel::remove(pedidoModel::$t2, ['id' => $id], 1);

      // Recalcular los elementos del pedido
      pedidoModel::recalculate_by_id($item['id_pedido']);

      // Regresar la respuesta
      json_output(json_build(200, $producto, 'Producto quitado del pedido.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para actualizar el proveedor de un
   * pedido actualmente en curso
   *
   */
  function do_update_proveedor_pedido()
  {
    try {
      if (!check_posted_data(['id', 'id_pedido', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada para el usuario.');
      }

      $id        = clean($_POST['id']); // id del proveedor
      $id_pedido = clean($_POST['id_pedido']); // id del pedido en curso

      // Validar que exista el pedido en la base de datos
      if (!$pedido = pedidoModel::by_id($id_pedido)) {
        json_output(json_build(400, null, 'No existe el pedido en la base de datos.'));
      }

      // Validar el status del pedido, si es completado no se deben permitir cambios
      if (!in_array($pedido['status'], ['borrador']) && !empty($pedido['razon_social'])) {
        json_output(json_build(400, null, 'No es posible cambiar el proveedor de este pedido ya.'));
      }

      // Validar que exista el proveedor en existencia
      if (!$proveedor = proveedorModel::by_id($id)) {
        json_output(json_build(400, null, 'No existe el proveedor en la base de datos.'));
      }

      // Actualizar el proveedor del pedido en la base de datos
      pedidoModel::update(pedidoModel::$t1, ['id' => $id_pedido], ['id_proveedor' => $proveedor['id']]);

      // Regresar la respuesta
      json_output(json_build(200, $proveedor, 'Proveedor del pedido actualizado con éxito.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  function do_save_notas_pedido()
  {
    try {
      if (!check_posted_data(['id', 'notas', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id    = clean($_POST['id']); // id del proveedor
      $notas = clean($_POST['notas']); // id del pedido en curso

      // Validar que exista el pedido en la base de datos
      if (!$pedido = pedidoModel::by_id($id)) {
        json_output(json_build(400, null, 'No existe el pedido en la base de datos.'));
      }

      // Validar el status del pedido, si es cancelado no es posible hacer cambios ya
      if (in_array($pedido['status'], ['cancelado'])) {
        json_output(json_build(400, null, 'No es posible actualizar las notas del pedido.'));
      }

      // Si no hay cambios en las notas
      if ($notas == $pedido['notas']) {
        json_output(json_build(400, null, 'No hubo cambios en las notas del pedido.'));
      }

      // Actualizar el proveedor del pedido en la base de datos
      pedidoModel::update(pedidoModel::$t1, ['id' => $id], ['notas' => $notas]);

      // Regresar la respuesta
      $pedido = pedidoModel::by_id($id);
      json_output(json_build(200, $pedido, 'Notas actualizadas con éxito.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para actualizar o sincronizar en caso de nuevos precios en productos
   * para no tener que borrar el pedido o producto manualmente para actualizar
   * su precio en el pedido actual.
   *
   */
  function do_recalcular_precios()
  {
    try {
      if (!check_posted_data(['id', 'csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Hubo un error, intenta más tarde.');
      }

      $id = clean($_POST['id']);

      // Validar que exista el pedido en la base de datos
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new PDOException('No existe el pedido en la base de datos.');
      }

      $productos = $pedido['productos'];

      // Validar status del pedido
      if (!in_array($pedido['status'], ['borrador'])) {
        throw new Exception('Ya no puedes actualizar los productos en este pedido.');
      }

      // Validar que existan productos en el pedido para actualizar
      if (empty($productos)) {
        throw new Exception('No hay productos para actualizar.');
      }

      // Iterar sobre los productos en el pedido
      foreach ($productos as $p) {
        // Cargar información nueva de cada producto
        if (!$producto = productoModel::by_id($p['id_producto'])) {
          pedidoModel::remove(pedidoModel::$t2, ['id' => $p['id']]);
          logger(sprintf('El producto %s no se encontró en la base de datos, se ha removido del pedido #%s', $p['nombre_original'], $pedido['numero']));
          continue;
        }

        // Actulizar el registro en pedidos_productos
        $variante = $p['variante'];
        $extra    = in_array($variante, ['XL','xl','+20']);
        $precio   = $producto['precio'] + ($extra ? 20 : 0);
        $cantidad = $p['cantidad'];
        $total    = $precio * $cantidad;
        $subtotal = $total / get_taxes_rate();
        $data     =
        [
          'precio'   => $precio,
          'cantidad' => $cantidad,
          'subtotal' => $subtotal,
          'total'    => $total
        ];
  
        pedidoModel::update(pedidoModel::$t2, ['id' => $p['id'], 'id_producto' => $producto['id'], 'id_pedido' => $id], $data);
      }

      // Recalcular pedido
      pedidoModel::recalculate_by_id($id);

      // Respuesta
      json_output(json_build(200, $pedido, 'Pedido recalculado con éxito.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para procesar y optimizar las imágenes de todos los productos
   *
   */
  function do_optimizar_imagenes()
  {
    try {
      if (!check_posted_data(['csrf'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Acceso no autorizado.');
      }

      if (!is_admin()) {
        throw new Exception('Acceso no autorizado.');
      }

      // Validar que existan productos
      $optimized = 0;
      $skipped   = 0;
      $errors    = 0;
      $productos = productoModel::all();
      $size_w    = 200;

      if (empty($productos)) {
        throw new Exception('No hay productos para procesar imágenes.');
      }

      // Iterar sobre todos los productos
      foreach ($productos as $p) {
        $imagen = $p['imagen'];

        // Verificar que incluyan una imagen de producto
        if ($imagen == null || empty($imagen)) {
          $skipped++;
          continue;
        }

        // Verificar que la imagen exista
        if (!is_file(UPLOADS.$imagen)) {
          productoModel::update(productoModel::$t1, ['id' => $p['id']], ['imagen' => '']);
          $skipped++;
          continue;
        }

        // Verificar el tamaño de la imagen
        list($width, $height, $type, $attr) = @getimagesize(UPLOADS.$imagen);

        if ($width <= 200) {
          $skipped++;
          continue;
        }

        // Optimizar la imagen y volver a guardarla en el servidor con los cambios
        $handler = new Upload(UPLOADS.$imagen);
        
        if (!$handler->uploaded) {
          $errors++;
          continue;
        }

        $handler->file_new_name_body = generate_filename();
        $handler->file_overwrite     = true;
        $handler->image_resize       = true;
        $handler->image_ratio_y      = true;
        $handler->image_x            = $size_w;
  
        $handler->process(UPLOADS);
        if (!$handler->processed) {
          $errors++;
          continue;
        }

        $filename = $handler->file_dst_name; // nombre de la imagen guardada en el servidor
        $handler->clean();

        // Actualizar el nombre en la base de datos
        productoModel::update(productoModel::$t1, ['id' => $p['id']], ['imagen' => $filename]);

        $optimized++;

        // Remover la imagen original
        if (is_file(UPLOADS.$imagen)) {
          unlink(UPLOADS.$imagen);
        }
      }

      // Mensaje
      $msg = '';

      if ($optimized > 0) {
        $msg .= sprintf('Hemos optimizado %s imágenes con éxito. ', $optimized);
      }

      if ($skipped > 0) {
        $msg .= sprintf('Se saltaron %s archivos. ', $skipped);
      }

      if ($errors > 0) {
        $msg .= sprintf('Hubo %s errores encontrados.', $errors);
      }

      // Regresar respuesta
      json_output(json_build(200, null, $msg));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para actualizar en bulk todos los precios de productos con base al valor pasado
   *
   */
  function do_update_prices()
  {
    try {
      if (!check_posted_data(['csrf','precio_actual','precio_nuevo'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Acceso no autorizado.');
      }

      if (!is_admin()) {
        throw new Exception('Acceso no autorizado.');
      }

      // Valores
      $precio_actual = clean($_POST["precio_actual"]);
      $precio_actual = (float) $precio_actual;
      $precio_nuevo  = clean($_POST["precio_nuevo"]);
      $precio_nuevo  = (float) $precio_nuevo;

      // Validaciones de precio
      if ($precio_actual <= 0) {
        throw new Exception('Ingresa un precio actual válido, debe ser mayor a 0.');
      }

      if ($precio_nuevo <= 0) {
        throw new Exception('Ingresa un precio nuevo válido, debe ser mayor a 0.');
      }
      
      $productos     = productoModel::by_price($precio_actual);
      $updated       = 0;
      $errors        = 0;

      if (empty($productos)) {
        throw new Exception('No hay productos disponibles con ese precio.');
      }

      // Iterar sobre todos los productos
      foreach ($productos as $p) {
        // Actualizar el precio de cada producto
        if (!productoModel::update(productoModel::$t1, ['id' => $p['id']], ['precio' => $precio_nuevo])) {
          $errors++;
          continue;
        }

        $updated++;
      }

      // Mensaje
      $msg = '';

      if ($updated > 0) {
        $msg .= sprintf('Hemos actualizado %s productos con éxito. ', $updated);
      }

      if ($errors > 0) {
        $msg .= sprintf('Hubo %s errores encontrados.', $errors);
      }

      // Regresar respuesta
      json_output(json_build(200, null, $msg));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Registrar el pago de un pedido
   * @version 1.3.0
   * @return mixed
   */
  function do_pay_pedido()
  {
    try {
      if (!check_posted_data(['csrf','total_pagado','status_pago','metodo_pago','id'], $_POST) || !Csrf::validate($_POST['csrf'])) {
        throw new Exception('Acceso no autorizado.');
      }

      if (!is_admin()) {
        throw new Exception('Acceso no autorizado.');
      }

      // Valores
      array_map('clean', $_POST);
      $id           = $_POST["id"];
      $status_pago  = $_POST["status_pago"];
      $total_pagado = unmoney($_POST["total_pagado"]);
      $metodo_pago  = $_POST["metodo_pago"];

      // Validar parámetros enviados
      if ($metodo_pago === 'none') {
        throw new Exception('Selecciona un método de pago válido por favor.');
      }

      if ($status_pago === 'none') {
        throw new Exception('Selecciona un estado de pago válido por favor.');
      }

      // Validar que exista el pedido en curso
      if (!$pedido = pedidoModel::by_id($id)) {
        throw new Exception('El pedido que buscas no existe.');
      }

      // Validar el status del pedido
      if ($pedido['status'] === 'borrador') {
        throw new Exception('El pedido actual se encuentra en borrador, no podemos registrar el pago.');
      }

      if ($pedido['status'] === 'cancelado') {
        throw new Exception('El pedido actual se encuentra cancelado, no podemos registrar el pago.');
      }

      // Data a ser actualizada en la base de datos
      $data =
      [
        'total_pagado' => $total_pagado,
        'metodo_pago'  => $metodo_pago,
        'status_pago'  => $status_pago
      ];

      if (in_array($status_pago, ['parcial','pagado'])) {
        $data['fecha_pago'] = now();
      } else {
        $data['fecha_pago'] = null;
      }

      // Actualizar la información del pedido
      if (!pedidoModel::update(pedidoModel::$t1, ['id' => $id], $data)) {
        throw new Exception('Hubo un problema al actualizar el registro.');
      }

      // Pedido
      $pedido = pedidoModel::by_id($id);

      // Regresar respuesta
      json_output(json_build(200, $pedido, 'Pago actualizado con éxito.'));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    } catch(Exception $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para cargar la información que
   * se utilizará para dibujar la gráfica de los
   * productos más solicitados o destacados
   *
   */
  function chart_top_products()
  {
    try {
      json_output(json_build(200, productoModel::best_selling()));
    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }

  /**
   * Función para mostrar la lista de productos
   * más solicitados en Ordify para la sección
   * de estadísticas
   *
   */
  function recargar_top_products()
  {
    try {
      // Cargar los productos más solicitados
      $top_productos = productoModel::best_selling(50);

      // Formatear el modulo a regresar a la vista
      $html = get_module('estadisticas/topProductos', $top_productos);

      json_output(json_build(200, $html));

    } catch(PDOException $e) {
      json_output(json_build(400, null, $e->getMessage()));
    }
  }
}