<?php

/**
 * Plantilla general de modelos
 * Versión 1.0.0
 *
 * Modelo de post
 */
class postModel extends Model {
  public static $t1 = 'posts';
  
  function __construct()
  {
    // Constructor general
  }
  
  static function all() {
    // Todos los registros
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM posts WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }
}

