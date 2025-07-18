<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.0
 *
 * Modelo de producto
 */
class productoModel extends Model {
  static $t1 = 'productos';
  
  function __construct()
  {
    // Constructor general
  }
  
  static function all() 
  {
    // Todos los registros
    $sql = 'SELECT * FROM productos ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    $sql = 'SELECT * FROM productos ORDER BY id DESC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM productos WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }

  static function by_price($price)
  {
    // Todos los registros
    $sql = 'SELECT * FROM productos WHERE CAST(precio AS DECIMAL) = CAST(:precio AS DECIMAL) ORDER BY id DESC';
    return ($rows = parent::query($sql, ['precio' => (string) $price])) ? $rows : [];
  }

  static function serch($term)
  {
    $serch_term = sprintf('%%%s%%', $term);

    // Productos
    $sql = 'SELECT DISTINCT p.* FROM productos p WHERE p.nombre LIKE :nombre OR p.sku LIKE :sku OR p.descripcion LIKE :desc ORDER BY p.id DESC';
    return ($rows = proveedorModel::query($sql, ['nombre' => $serch_term, 'sku' => $serch_term, 'desc' => $serch_term])) ? $rows : [];
  }

  static function best_selling($cantidad = 10)
  {
    $cantidad = $cantidad > 100 ? 10 : $cantidad;
    $sql      = "SELECT
      p.nombre AS producto,
      pp.nombre AS pp_producto,
      pp.variante,
      pp.corte,
      SUM(pp.cantidad) AS total,
      p.imagen
    FROM
      pedidos_productos pp
    JOIN productos p ON p.id = pp.id_producto
    JOIN pedidos pe ON pe.id = pp.id_pedido
    WHERE pe.status IN ('completado')
    GROUP BY
      pp.nombre,
      pp.id_producto
    ORDER BY
      total DESC
    LIMIT $cantidad";

    return parent::query($sql);
  }
}

