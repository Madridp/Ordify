<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de proveedores
 */
class proveedoresController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
  }
  
  function index() 
  {
    // Descomentar vista si requerida
    $data =
    [
      'title'       => 'Proveedores',
      'slug'        => 'proveedores',
      'proveedores' => proveedorModel::all_paginated()
    ];

    View::render('index', $data);
  }

  function agregar()
  {
    $data =
    [
      'title'       => 'Agregar proveedor',
      'slug'        => 'proveedores'
    ];

    View::render('agregar', $data);
  }

  function post_agregar()
  {
    if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['nombre','razon_social','email','rfc','telefono','direccion'], $_POST)) {
      Flasher::deny();
      Redirect::back();
    }

    try {
      // Validación del correo electrónico
      if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new PDOException('Dirección de email no válida.');
      }

      // Validación del RFC, no debe existir en la base de datos
      if (proveedorModel::list('proveedores', ['rfc' => clean($_POST['rfc'])])) {
        throw new PDOException(sprintf('Ya existe el RFC <b>%s</b> en la base de datos.', $_POST['rfc']));
      }

      $id   = null;
      $data =
      [
        'nombre'       => clean($_POST['nombre']),
        'razon_social' => clean($_POST['razon_social']),
        'rfc'          => clean($_POST['rfc']),
        'email'        => clean($_POST['email']),
        'telefono'     => clean($_POST['telefono']),
        'direccion'    => clean($_POST['direccion']),
        'creado'       => now()
      ];

      if (!$id = proveedorModel::add('proveedores', $data)) {
        throw new PDOException('Hubo un problema al agregar el registro.');
      }

      Flasher::new(sprintf('Nuevo proveedor <b>%s</b> agregado con éxito.', $data['razon_social']), 'success');
      Redirect::to('proveedores');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function ver($id)
  {    
    if (!$p = proveedorModel::list('proveedores', ['id' => $id], 1)) {
      Flasher::new('El proveedor no existe.', 'danger');
      Redirect::to('proveedores');
    }

    $data =
    [
      'title' => sprintf('Viendo %s', $p['razon_social']),
      'slug'  => 'proveedores',
      'p'     => $p
    ];

    View::render('ver', $data);
  }

  function editar($id)
  {
    if (!$p = proveedorModel::list('proveedores', ['id' => $id], 1)) {
      Flasher::new('El proveedor no existe.', 'danger');
      Redirect::to('proveedores');
    }

    $data =
    [
      'title' => sprintf('Editando %s', $p['razon_social']),
      'slug'  => 'proveedores',
      'p'     => $p
    ];

    View::render('editar', $data);
  }

  function post_editar()
  {
    try {
      if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['id','nombre','razon_social','email','rfc','telefono','direccion'], $_POST)) {
        Flasher::deny();
        Redirect::back();
      }

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada para el usuario.');
      }

      // Validar el correo electrónico
      if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new PDOException('Dirección de email no válida.');
      }

      // Validar que no exista el RFC en la base de datos
      $id  = clean($_POST['id']);
      $sql = 'SELECT * FROM proveedores WHERE rfc = :rfc AND id != :id';
      if (proveedorModel::query($sql, ['rfc' => clean($_POST['rfc']), 'id' => $id])) {
        throw new PDOException(sprintf('Ya existe el RFC <b>%s</b> en la base de datos.', $_POST['rfc']));
      }

      $data =
      [
        'nombre'       => clean($_POST['nombre']),
        'razon_social' => clean($_POST['razon_social']),
        'rfc'          => clean($_POST['rfc']),
        'email'        => clean($_POST['email']),
        'telefono'     => clean($_POST['telefono']),
        'direccion'    => clean($_POST['direccion'])
      ];

      if (!proveedorModel::update('proveedores', ['id' => $id], $data)) {
        throw new PDOException('Hubo un problema al actualizar el registro.');
      }

      Flasher::new(sprintf('Proveedor %s actualizado con éxito.', $data['razon_social']), 'success');
      Redirect::to('proveedores');

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

      if (!$proveedor = proveedorModel::by_id($id)) {
        throw new PDOException('El proveedor no existe en la base de datos.');
      }

      if (!proveedorModel::remove('proveedores', ['id' => $id], 1)) {
        throw new PDOException('Hubo un problema al borrar el registro.');
      }

      Flasher::new(sprintf('Proveedor <b>%s</b> borrado con éxito.', $proveedor['razon_social']), 'success');
      Redirect::to('proveedores');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }
}

