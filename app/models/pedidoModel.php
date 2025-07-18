<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.0
 *
 * Modelo de pedido
 */
class pedidoModel extends Model {
  static $t1 = 'pedidos';
  static $t2 = 'pedidos_productos';

  function __construct()
  {
    // Constructor general
  }
  
  static function all() 
  {
    // Todos los registros
    $sql = 'SELECT
    p.* ,
    pr.nombre,
    pr.email,
    pr.razon_social,
    pr.telefono,
    pr.rfc,
    (SELECT COUNT(pp.id) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_productos,
    (SELECT SUM(pp.cantidad) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_cantidades,
    (SELECT SUM(pp.recibidos) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_recibidos,
    (SELECT SUM(pp.danados) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_rechazados,
    (SELECT SUM(pp.cancelados) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_cancelados,
    (SELECT COALESCE(SUM(pp.recibidos + pp.danados + pp.cancelados), 0) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_procesados
    FROM pedidos p
    LEFT JOIN proveedores pr ON p.id_proveedor = pr.id
    ORDER BY p.id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    $sql = 'SELECT
    p.* ,
    pr.nombre,
    pr.email,
    pr.razon_social,
    pr.telefono,
    pr.rfc,
    (SELECT COUNT(pp.id) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_productos,
    (SELECT SUM(pp.cantidad) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_cantidades,
    (SELECT SUM(pp.recibidos) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_recibidos,
    (SELECT SUM(pp.danados) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_rechazados,
    (SELECT SUM(pp.cancelados) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_cancelados,
    (SELECT COALESCE(SUM(pp.recibidos + pp.danados + pp.cancelados), 0) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_procesados
    FROM pedidos p
    LEFT JOIN proveedores pr ON p.id_proveedor = pr.id
    ORDER BY p.id DESC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT
    p.*,
    pr.nombre,
    pr.email,
    pr.razon_social,
    pr.telefono,
    pr.rfc,
    pr.direccion,
    (SELECT COUNT(pp.id) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_productos,
    (SELECT COALESCE(SUM(pp.cantidad), 0) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_cantidades,
    (SELECT COALESCE(SUM(pp.recibidos), 0) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_recibidos,
    (SELECT COALESCE(SUM(pp.danados), 0) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_rechazados,
    (SELECT COALESCE(SUM(pp.cancelados), 0) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_cancelados,
    (SELECT COALESCE(SUM(pp.recibidos + pp.danados + pp.cancelados), 0) FROM pedidos_productos pp WHERE pp.id_pedido = p.id) AS total_procesados
    FROM pedidos p
    LEFT JOIN proveedores pr ON p.id_proveedor = pr.id
    WHERE p.id = :id';

    $pedido = parent::query($sql, ['id' => $id]);

    if (!$pedido) return [];

    // Buscar productos en pedido
    $pedido              = $pedido[0];
    $pedido['productos'] = self::get_items($id);

    // Buscar adjuntos en el pedido
    $pedido['adjuntos']  = self::get_adjuntos($id);

    return $pedido;
  }

  static function get_items($id_pedido)
  {
    $sql =
    'SELECT
    pp.*,
    p.imagen,
    p.nombre AS nombre_original,
    p.corte,
    p.sku,
    p.adjuntos
    FROM pedidos_productos pp
    LEFT JOIN productos p ON pp.id_producto = p.id
    WHERE pp.id_pedido = :id_pedido ORDER BY p.id DESC, pp.id DESC';
    return ($rows = parent::query($sql, ['id_pedido' => $id_pedido])) ? $rows : [];
  }

  static function get_adjuntos($id_pedido)
  {
    $sql =
    'SELECT
    p.*
    FROM posts p
    LEFT JOIN usuarios u ON p.id_usuario = u.id
    WHERE p.tipo = "adjunto" AND p.id_ref = :id_pedido
    ORDER BY p.id DESC';

    return ($rows = parent::query($sql, ['id_pedido' => $id_pedido])) ? $rows : [];
  }

  static function total_by_year($meses = 12)
  {
    $months = $meses >= 36 ? 36 : $meses;
    $ykey   = 'id';
    $xkey   = 'creado';

    // $sql    = "SELECT Months.mm AS mes, 
    // Months.m AS month, 
    // COUNT(p.id) AS total 
    // FROM 
    // (
    //   SELECT 1 as m, 'Enero' AS mm
    //   UNION SELECT 2 as m, 'Febrero' AS mm 
    //   UNION SELECT 3 as m, 'Marzo' AS mm 
    //   UNION SELECT 4 as m, 'Abril' AS mm 
    //   UNION SELECT 5 as m, 'Mayo' AS mm 
    //   UNION SELECT 6 as m, 'Junio' AS mm 
    //   UNION SELECT 7 as m, 'Julio' AS mm 
    //   UNION SELECT 8 as m, 'Agosto' AS mm 
    //   UNION SELECT 9 as m, 'Septiembre' AS mm 
    //   UNION SELECT 10 as m, 'Octubre' AS mm 
    //   UNION SELECT 11 as m, 'Noviembre' AS mm 
    //   UNION SELECT 12 as m, 'Diciembre' AS mm
    // ) as Months
    // LEFT JOIN pedidos p on Months.m = MONTH(p.fecha_inicio) 
    // AND YEAR(p.fecha_inicio) = :ano GROUP BY Months.m";

    $sql = "SELECT 
    COUNT(p.$ykey) AS total, 
    DATE_FORMAT(m.merge_date,'%M %Y') AS fecha,
    DATE_FORMAT(m.merge_date,'%M') AS mes,
    YEAR(m.merge_date) AS año
    FROM (";

    if($months == 1){
      $sql .= " SELECT CURRENT_DATE AS merge_date ";
    } else {
      for ($i=0; $i < intval($months); $i++) { 
        $sql .= ($i == 0 ? "" : " UNION ")." SELECT CURRENT_DATE ".($i == 0 ? "" : "- INTERVAL ".$i." MONTH")." AS merge_date";
      }
    }

    $sql .= ") AS m 
    LEFT JOIN pedidos p ON MONTH(m.merge_date) = MONTH(p.$xkey) AND YEAR(m.merge_date) = YEAR(p.$xkey) AND p.status NOT IN('borrador','cancelado')
    GROUP BY m.merge_date 
    ORDER BY DATE_FORMAT(m.merge_date , '%Y %m') ASC";

    $rows = parent::query($sql);

    foreach ($rows as $i => $row) {
      $row['mes'] = translate_month($row['mes']);
      $rows[$i]   = $row;
    }

    return $rows;
  }

  static function total_by_year2($año = null)
  {
    $año = $año === null ? date('Y') : $año;
    $sql = "SELECT Months.mm AS mes, 
    Months.m AS month, 
    COUNT(p.id) AS total 
    FROM 
    (
      SELECT 1 as m, 'Enero' AS mm
      UNION SELECT 2 as m, 'Febrero' AS mm 
      UNION SELECT 3 as m, 'Marzo' AS mm 
      UNION SELECT 4 as m, 'Abril' AS mm 
      UNION SELECT 5 as m, 'Mayo' AS mm 
      UNION SELECT 6 as m, 'Junio' AS mm 
      UNION SELECT 7 as m, 'Julio' AS mm 
      UNION SELECT 8 as m, 'Agosto' AS mm 
      UNION SELECT 9 as m, 'Septiembre' AS mm 
      UNION SELECT 10 as m, 'Octubre' AS mm 
      UNION SELECT 11 as m, 'Noviembre' AS mm 
      UNION SELECT 12 as m, 'Diciembre' AS mm
    ) as Months
    LEFT JOIN pedidos p on Months.m = MONTH(p.fecha_inicio) 
    AND YEAR(p.fecha_inicio) = :ano GROUP BY Months.m";

    return parent::query($sql, ['ano' => $año]);
  }

  static function item_by_id($id_item)
  {
    $sql =
    'SELECT
    pp.*,
    p.imagen,
    p.nombre AS nombre_original,
    p.corte
    FROM pedidos_productos pp
    LEFT JOIN productos p ON pp.id_producto = p.id
    WHERE pp.id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id_item])) ? $rows[0] : [];
  }

  static function recalculate_by_id($id)
  {
    $pedido   = self::by_id($id);
    $subtotal = 0;
    $total    = 0;
    $piezas   = 0;

    // Si no existe el pedido
    if (empty($pedido)) return false;

    // Validar que existan productos en el pedido
    if (empty($pedido['productos'])) return false;

    // Iterar sobre los productos 
    foreach ($pedido['productos'] as $p) {
      $total  += $p['cantidad'] * $p['precio'];
      $piezas += $p['cantidad'];
    }

    $subtotal = $total / get_taxes_rate(); // con base al valor de impuestos cobrados

    // Actualizar el registro en la base de datos
    pedidoModel::update(pedidoModel::$t1, ['id' => $id], ['subtotal' => $subtotal, 'total' => $total]);
    return true;
  }

  static function total_inversion_by_year($meses = 12)
  {
    $months = $meses >= 36 ? 36 : $meses;
    $ykey   = 'id';
    $xkey   = 'creado';

    $sql = "SELECT 
    COALESCE(SUM(p.total), 0) AS total, 
    DATE_FORMAT(m.merge_date,'%M %Y') AS fecha,
    DATE_FORMAT(m.merge_date,'%M') AS mes,
    YEAR(m.merge_date) AS año
    FROM (";

    if($months == 1){
      $sql .= " SELECT CURRENT_DATE AS merge_date ";
    } else {
      for ($i=0; $i < intval($months); $i++) { 
        $sql .= ($i == 0 ? "" : " UNION ")." SELECT CURRENT_DATE ".($i == 0 ? "" : "- INTERVAL ".$i." MONTH")." AS merge_date";
      }
    }

    $sql .= ") AS m 
    LEFT JOIN pedidos p ON MONTH(m.merge_date) = MONTH(p.$xkey) AND YEAR(m.merge_date) = YEAR(p.$xkey) AND p.status NOT IN('borrador','cancelado')
    GROUP BY m.merge_date 
    ORDER BY DATE_FORMAT(m.merge_date , '%Y %m') ASC";

    $rows = parent::query($sql);

    foreach ($rows as $i => $row) {
      $row['mes'] = translate_month($row['mes']);
      $rows[$i]   = $row;
    }

    return $rows;
  }

  static function remove_by_id($id)
  {
    $sql = 'DELETE p, pp, pt FROM pedidos p
    LEFT JOIN pedidos_productos pp ON pp.id_pedido = p.id
    LEFT JOIN posts pt ON pt.id_ref = p.id AND pt.tipo IN("tracking","pago","comentario")
    WHERE p.id = :id';

    return (parent::query($sql, ['id' => $id])) ? true : false;
  }
}

