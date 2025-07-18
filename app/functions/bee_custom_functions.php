<?php 

/**
 * Función para formatear las variantes de un producto
 *
 * @param string $variantes
 * @return mixed
 */
function format_variantes($variantes = null)
{
  if ($variantes === null || empty($variantes)) {
    return false;
  }

  // Iterar sobre las variantes
  $output    = '';
  $variantes = explode('|', $variantes);

  foreach ($variantes as $v) {
    $output .= sprintf('<span class="badge rounded-pill bg-primary" style="margin-right: 3px;">%s</span>', $v);
  }

  return $output;
}

/**
 * Diferentes cortes de prenda aceptados
 * Esto puede ser sustituido por registros de la base de datos
 * sin mayor complicación de implementación
 *
 * @return array
 */
function get_cortes()
{
  return 
  [
    'VA Manga corta',
    'VA Manga larga',
    'VA Regata dama',
    'VA Regata caballero',
    'VA brush manga corta',
    'Infantil manga corta',
    'Infantil manga larga',
    'Leggins dama'
  ];
}

/**
 * Regresa todos los estados posibles para un pedido
 *
 * @return array
 */
function get_estados_pedido()
{
  return
  [
    ['borrador'  , 'Borrador'],
    ['pendiente' , 'Pendiente'],
    ['en_camino' , 'En Camino'],
    ['recibido'  , 'Recibido'],
    ['procesando', 'Procesando'],
    ['completado', 'Completado'],
    ['cancelado' , 'Cancelado']
  ];
}

/**
 * Formatea el estado o status de un pedido dependiendo del string pasado
 * dicho status debe provenir de la base de datos
 * Para cambiar el ícono o textos por defecto puedes editar esta función y la anterior
 * para lo estados mostrados en select inputs
 *
 * @param string $estado
 * @return string
 */
function format_estado_pedido($estado)
{
  $text    = null;
  $classes = null;
  $icon    = null;

  switch ($estado) {
    case 'borrador':
      $text    = 'Borrador';
      $classes = 'badge rounded-pill bg-info';
      $icon    = 'fas fa-eraser';
      break;

    case 'pendiente':
      $text    = 'Pendiente';
      $classes = 'badge rounded-pill bg-danger';
      $icon    = 'fas fa-clock';
      break;

    case 'en_camino':
      $text    = 'En Camino';
      $classes = 'badge rounded-pill bg-warning text-dark';
      $icon    = 'fas fa-truck';
      break;
      
    case 'recibido':
      $text    = 'Recibido';
      $classes = 'badge rounded-pill bg-primary';
      $icon    = 'fas fa-pallet';
      break;

    case 'procesando':
      $text    = 'Procesando';
      $classes = 'badge rounded-pill bg-primary';
      $icon    = 'fas fa-truck-loading';
      break;

    case 'completado':
      $text    = 'Completado';
      $classes = 'badge rounded-pill bg-success';
      $icon    = 'fas fa-check';
      break;

    case 'cancelado':
      $text    = 'Cancelado';
      $classes = 'badge rounded-pill bg-danger';
      $icon    = 'fas fa-ban';
      break;

    default:
      $text = 'Desconocido';
      $classes = 'badge rounded-pill bg-warning text-dark';
      $icon    = 'fas fa-question';
      break;
  }

  return sprintf('<span class="%s"><i class="%s"></i> %s</span>', $classes, $icon, $text);
}



/**
 * Función para cargar las variaciones de cada producto 
 * en una lista que será utilizada para manejar el campo select en agregar nuevos productos
 * a pedido en Ordify
 *
 * @return void
 */
function get_select_productos()
{
  $productos = productoModel::all();
  $data      = [];

  if (empty($productos)) return [];

  // Iterar sobre los productos y extraer cada variante posible
  foreach ($productos as $p) {
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

  return $data;
}

/**
 * Regresa el valor de los impuestos
 * cobrados en el precio de cada producto
 *
 * @return float
 */
function get_taxes_rate()
{
  return 1.16;
}

/**
 * Envía al proveedor un email con
 * el resumen del pedido actual realizado
 *
 * @param integer $id_pedido
 * @return void
 */
function send_email_nuevo_pedido($id_pedido)
{
  try {
    if (!$pedido = pedidoModel::by_id($id_pedido)) return false;
    if (!$proveedor = proveedorModel::by_id($pedido['id_proveedor'])) return false;
  
    $email         = $proveedor['email'];
    $subject       = sprintf('[%s] - Nuevo pedido solicitado %s', get_sitename(), $pedido['numero']);
    $alt           = sprintf('Nuevo pedido %s solicitado.', $pedido['numero']);
    $url           = sprintf(URL.'p/pedido/%s', $pedido['hash']);
    $pedido['url'] = $url;
    $body          = get_module('emails/nuevoPedido', $pedido);
    
    send_email(get_siteemail(), $email, $subject, $body, $alt);
    return true;
    
  } catch (Exception $e) {
    return false;
  }
}

/**
 * Función para calcular el porcentaje de recepción
 * y asignaran una clase a la fila en una tabla
 *
 * @param integer $c_total
 * @param integer $c_recibidos
 * @return string
 */
function format_clases_producto_pedido($c_total, $c_recibidos)
{
  // Calcular porcentaje de diferencia
  $porcentaje = (float) ($c_recibidos * 100) / $c_total;
  $classes    = '';

  switch (true) {
    case $porcentaje > 100:
      $classes = 'row-border row-border-primary';
      break;

    case $porcentaje == 100:
      $classes = 'row-border row-border-success';
      break;

    case 50 < $porcentaje:
      $classes = 'row-border row-border-warning';
      break;

    case 0 < $porcentaje:
      $classes = 'row-border row-border-warning2';
      break;
    
    case 0 == $porcentaje:
      $classes = 'row-border row-border-danger';
      break;
    
    default:
      $classes = 'row-border row-border-default';
      break;
  }

  return $classes;
}

/**
 * Determina si el sistema está en modo demostración o no
 * para limitar el acceso o la interacción de los usuarios y funcionalidades
 *
 * @return boolean
 */
function is_demo()
{
	return IS_DEMO;
}

/**
 * Función para validar si el sistema es una demostración
 * y guardar una notificación flash y redirigir al usuario
 * de así solicitarlo
 *
 */
function check_if_demo($flash = true, $redirect = true)
{
  $demo = is_demo();

  if ($demo == false) return false;

  if ($flash == true) {
    Flasher::new(sprintf('No disponible en la versión de demostración de %s.', get_sitename()), 'danger');
  }

  if ($redirect == true) {
    Redirect::back();
  }

  return true;
}

/**
 * Regresa la URL del logotipo
 *
 * @return string
 */
function get_ordify_logo()
{
  return IMAGES.'logo_500.png';
}

/**
 * Determina si el usuario en curso es administrador o no
 *
 * @return boolean
 */
function is_admin()
{
  $role = get_user('role');

  return $role === 'admin';
}

/**
 * Regresa un array de los posibles roles disponibles
 *
 * @return array
 */
function get_roles()
{
  return
  [
    ['admin'  , 'Administrador'],
    ['regular', 'Usuario regular']
  ];
}

/**
 * Regresa como string formateado el rol del usuario
 *
 * @param string $role
 * @return string
 */
function format_user_role($role)
{
  $classes     = '';
  $text        = '';
  $placeholder = '<span class="badge rounded-pill %s">%s</span>';

  switch ($role) {
    case 'regular':
      $classes = 'bg-primary';
      $text    = 'Usuario regular';
      break;

    case 'admin':
      $classes = 'bg-success';
      $text    = 'Administrador';
      break;
    
    default:
      $classes = 'bg-warning';
      $text    = 'Desconocido';
      break;
  }

  return sprintf($placeholder, $classes, $text);
}

/**
 * Regresa las opciones disponibles para
 * movimientos de stock al recibir mercancía
 *
 * @return array
 */
function get_stock_movement_types()
{
  return
  [
    ['incoming', 'Entrada'],
    ['damaged' , 'Dañado'],
    ['canceled', 'Cancelado']
  ];
}

/**
 * Regresa como string y formato la cantidad de piezas dañadas o canceladas
 * de un determinado pedido
 *
 * @param integer $danadas
 * @param integer $canceladas
 * @return string
 */
function format_stock_damage($danadas, $canceladas)
{
  $msg        = 'Sin más información.';
  $danadas    = $danadas > 0 ? $danadas : 0;
  $canceladas = $canceladas > 0 ? $canceladas : 0;
  
  // Si hubo dañadas pero no canceladas
  if ($danadas > 0 && $canceladas === 0) {
    $msg = sprintf('Hubo %s piezas dañadas de este producto.', $danadas);
  }

  // Si no hubo dañadas pero si canceladas
  if ($danadas === 0 && $canceladas > 0) {
    $msg = sprintf('Hubo %s piezas canceladas o rechazadas de este producto.', $canceladas);
  }

  // Si hubo dañadas y canceladas
  if ($danadas > 0 && $canceladas > 0) {
    $msg = sprintf('Hubo %s piezas dañadas y %s canceladas o rechazadas de este producto.', $danadas, $canceladas);
  }

  return $msg;
}

/**
 * Método para cargar los métodos de pago
 * disponibles para los pedidos
 * aceptados por la mayoría de proveedores
 *
 * @return array
 */
function get_metodos_pago()
{
  $metodos_de_pago =
  [
    [
      'id'   => 1,
      'name' => 'Tarjeta de débito',
      'slug' => 'tarjeta_debito'
    ],
    [
      'id'   => 2,
      'name' => 'Tarjeta de crédito',
      'slug' => 'tarjeta_credito'
    ],
    [
      'id'   => 3,
      'name' => 'Efectivo',
      'slug' => 'efectivo'
    ],
    [
      'id'   => 4,
      'name' => 'Transferencia Bancaria SPEI',
      'slug' => 'spei'
    ],
    [
      'id'   => 5,
      'name' => 'Mercado Pago',
      'slug' => 'mercado_pago'
    ],
    [
      'id'   => 6,
      'name' => 'Crédito especial',
      'slug' => 'credito_especial'
    ]
  ];

  return $metodos_de_pago;
}

/**
 * Formatea el método del pago de un pedido para visualización
 * en tablas del sistema Ordify
 *
 * @param string $estado_pago
 * @return string
 */
function format_metodo_pago($metodo_pago)
{
  $text    = null;
  $classes = null;
  $icon    = null;

  switch ($metodo_pago) {
    case 'tarjeta_debito':
      $text    = 'Tarjeta de débito';
      $classes = 'badge rounded-pill bg-success';
      $icon    = 'fa-solid fa-credit-card';
      break;

    case 'tarjeta_credito':
      $text    = 'Tarjeta de crédito';
      $classes = 'badge rounded-pill bg-success';
      $icon    = 'fa-solid fa-credit-card';
      break;

    case 'efectivo':
      $text    = 'Efectivo';
      $classes = 'badge rounded-pill bg-success';
      $icon    = 'fas fa-money-bill-1';
      break;

    case 'spei':
      $text    = 'Transferencia Bancaria SPEI';
      $classes = 'badge rounded-pill bg-dark';
      $icon    = 'fas fa-building-columns';
      break;

    case 'mercado_pago':
      $text    = 'Mercado Pago';
      $classes = 'badge rounded-pill bg-primary';
      $icon    = 'fas fa-money-check';
      break;
      
    case 'credito_especial':
      $text    = 'Crédito Especial';
      $classes = 'badge rounded-pill bg-success';
      $icon    = 'fas fa-money-check-dollar';
      break;

    default:
      $text = 'Desconocido';
      $classes = 'badge rounded-pill bg-warning text-dark';
      $icon    = 'fas fa-question';
      break;
  }

  return sprintf('<span class="%s"><i class="%s"></i> %s</span>', $classes, $icon, $text);
}

/**
 * Carga los posibles estados disponibles para
 * un pago realizado a un pedido
 *
 * @return array
 */
function get_status_pago()
{
  $estados = 
  [
    [
      'id'   => 1,
      'slug' => 'pendiente',
      'name' => 'Pendiente de pago'
    ],
    [
      'id'   => 2,
      'slug' => 'pagado',
      'name' => 'Pago realizado'
    ],
    [
      'id'   => 3,
      'slug' => 'parcial',
      'name' => 'Parcialmente pagado'
    ],
    [
      'id'   => 4,
      'slug' => 'cancelado',
      'name' => 'Cancelado'
    ],
    [
      'id'   => 5,
      'slug' => 'reembolsado',
      'name' => 'Reembolsado'
    ],
  ];

  return $estados;
}

/**
 * Formatea el estado del pago de un pedido para visualización
 * en tablas del sistema Ordify
 *
 * @param string $estado_pago
 * @return string
 */
function format_estado_pago($estado_pago)
{
  $text    = null;
  $classes = null;
  $icon    = null;

  switch ($estado_pago) {
    case 'pendiente':
      $text    = 'Pendiente';
      $classes = 'badge rounded-pill bg-danger';
      $icon    = 'fas fa-clock';
      break;

    case 'parcial':
      $text    = 'Pacialmente pagado';
      $classes = 'badge rounded-pill bg-primary';
      $icon    = 'fas fa-check';
      break;

    case 'pagado':
      $text    = 'Pagado';
      $classes = 'badge rounded-pill bg-success';
      $icon    = 'fas fa-check';
      break;

    case 'cancelado':
      $text    = 'Cancelado';
      $classes = 'badge rounded-pill bg-danger';
      $icon    = 'fas fa-ban';
      break;

    case 'reembolsado':
      $text    = 'Reembolsado';
      $classes = 'badge rounded-pill bg-danger';
      $icon    = 'fas fa-undo';
      break;

    default:
      $text = 'Desconocido';
      $classes = 'badge rounded-pill bg-warning text-dark';
      $icon    = 'fas fa-question';
      break;
  }

  return sprintf('<span class="%s"><i class="%s"></i> %s</span>', $classes, $icon, $text);
}

function format_pedido_url($hash)
{
  return sprintf('%sp/pedido/%s', URL, $hash);
}

/**
 * Traducir las fechas que regresa en ingles SQL en los queries
 * para estadísticas
 *
 * @param string $mes
 * @return string
 */
function translate_month($mes)
{
  $meses =
  [
    ['January'  , 'Enero'],
    ['February' , 'Febrero'],
    ['March'    , 'Marzo'],
    ['April'    , 'Abril'],
    ['May'      , 'Mayo'],
    ['June'     , 'Junio'],
    ['July'     , 'Julio'],
    ['August'   , 'Agosto'],
    ['September', 'Septiembre'],
    ['October'  , 'Octubre'],
    ['November' , 'Noviembre'],
    ['December' , 'Diciembre']
  ];

  foreach ($meses as $m) {
    if ($mes == $m[0]) {
      return $m[1];
    }
  }

  return $mes;
}