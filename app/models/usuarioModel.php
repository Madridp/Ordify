<?php 

class usuarioModel extends Model
{
  static $t1 = 'usuarios';

  static function all()
  {
    $sql = 'SELECT * FROM usuarios ORDER BY id DESC';
    return ($rows = parent::query($sql)) ? $rows : [];
  }

  static function all_paginated()
  {
    $sql = 'SELECT * FROM usuarios ORDER BY id DESC';
    return PaginationHandler::paginate($sql);
  }

  static function by_id($id)
  {
    // Un registro con $id
    $sql = 'SELECT * FROM usuarios WHERE id = :id LIMIT 1';
    return ($rows = parent::query($sql, ['id' => $id])) ? $rows[0] : [];
  }
}