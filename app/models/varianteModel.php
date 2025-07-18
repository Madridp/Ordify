<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.0
 *
 * Modelo de variante
 */
class varianteModel extends Model {
  public static $t1 = 'posts';

  function __construct()
  {
    // Constructor general
  }
  
  static function all() {
    // Todos los registros
    $sql = 'SELECT p.* FROM posts p WHERE p.tipo IN("variante") ORDER BY p.id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    $sql = 'SELECT p.* FROM posts p WHERE p.tipo IN("variante") ORDER BY p.id DESC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT p.* FROM posts p WHERE p.tipo = "variante" AND p.id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }
}

