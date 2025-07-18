<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.0
 *
 * Modelo de proveedor
 */
class proveedorModel extends Model {
  function __construct()
  {
    // Constructor general
  }
  
  static function all() {
    // Todos los registros
    $sql = 'SELECT * FROM proveedores ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    $sql = 'SELECT * FROM proveedores ORDER BY id DESC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM proveedores WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }
}

