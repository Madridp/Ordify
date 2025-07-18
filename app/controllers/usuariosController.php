<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de usuarios
 */
class usuariosController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }

    // Bloquear el acceso a todas las rutas de usuario y administración de usuarios
    if (!is_admin()) {
      Flasher::deny();
      Redirect::to('home');
    }
  }
  
  function index()
  {
    global $Bee_session;

    $data =
    [
      'title'    => 'Usuarios',
      'usuarios' => usuarioModel::all_paginated()
    ];
    
    View::render('index', $data);
  }

  function ver($id)
  {
    if (!$usuario = usuarioModel::by_id($id)) {
      Flasher::new('No existe el usuario en la base de datos.', 'danger');
      Redirect::to('usuarios');
    }

    $data =
    [
      'title' => sprintf('Viendo %s', $usuario['nombre']),
      'slug'  => 'usuarios',
      'u'     => $usuario
    ];

    View::render('ver', $data);
  }

  function agregar()
  {
    $data =
    [
      'title' => 'Agregar usuario',
      'slug'  => 'usuarios'
    ];

    View::render('agregar', $data);
  }

  function post_agregar()
  {
    try {
      if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['nombre','role','usuario','email','password','password_conf','csrf'], $_POST)) {
        Flasher::deny();
        Redirect::back();
      }

      // Limitar el acceso si es una demo del sitio
      check_if_demo();

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Aasdcción no autorizada.');
      }

      $email     = clean($_POST['email']);
      $usuario   = clean($_POST['usuario']);
      $nombre    = clean($_POST['nombre']);
      $password  = clean($_POST['password']);
      $password2 = clean($_POST['password_conf']);
      $role      = clean($_POST["role"]);

      // Validación de role
      if (!in_array($role, ['admin','regular'])) {
        throw new Exception('El role seleccionado no es válido.');
      }

      // Validación del correo electrónico
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new PDOException('Dirección de email no válida.');
      }

      // Validar que no exista un usuario con ese usuario o email
      if (usuarioModel::list(usuarioModel::$t1, ['usuario' => $usuario])) {
        throw new PDOException(sprintf('Ya existe el usuario <b>%s</b> en la base de datos.', $usuario));
      }

      if (usuarioModel::list(usuarioModel::$t1, ['email' => $email])) {
        throw new PDOException(sprintf('Ya existe el correo electrónico <b>%s</b> en la base de datos.', $email));
      }

      // confirmar contraseña
      if ($password !== $password2) {
        throw new PDOException('Las contraseñas no coinciden, intenta de nuevo.');
      }

      // Guardar el registro en la base de datos
      $id   = null;
      $data =
      [
        'role'         => $role,
        'nombre'       => $nombre,
        'usuario'      => $usuario,
        'email'        => $email,
        'password'     => password_hash($password.AUTH_SALT, PASSWORD_BCRYPT),
        'creado'       => now()
      ];

      if (!$id = usuarioModel::add(usuarioModel::$t1, $data)) {
        throw new PDOException('Hubo un problema al agregar el registro.');
      }

      // Enviar notificación al usuario
      $placeholder = 
      'Datos de acceso a %s:<br>
      Usuario: <b>%s</b><br>
      Contraseña: <b>%s</b><br><br>
      No compartas esta información con nadie.<br><br>
      <a href="%s">Ingresar ahora</a>';
      $body = sprintf($placeholder, get_sitename(), $usuario, $password, URL.'login');
      send_email(get_siteemail(), $email, sprintf('Tus credenciales de acceso a %s', get_sitename()), $body, 'Ingresa al sistema con estas credenciales.');

      Flasher::new(sprintf('Nuevo usuario <b>%s</b> agregado con éxito.', $nombre), 'success');
      Redirect::to('usuarios');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function post_editar()
  {
    try {
      if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['nombre','role','usuario','email','password','password_conf','csrf','id'], $_POST)) {
        Flasher::deny();
        Redirect::back();
      }

      // Limitar el acceso si es una demo del sitio
      check_if_demo();

      // Solo permitir acción a administradores
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada.');
      }

      // Validar la existencia del registro
      $id = clean($_POST['id']);
      if (!$u = usuarioModel::by_id($id)) {
        throw new Exception('No existe el usuario en la base de datos.');
      }

      $db_nombre   = $u['nombre'];
      $db_usuario  = $u['usuario'];
      $db_email    = $u['email'];
      $db_password = $u['password'];
      $db_role     = $u['role'];
      $changed_pw  = false;

      $email       = clean($_POST['email']);
      $usuario     = clean($_POST['usuario']);
      $nombre      = clean($_POST['nombre']);
      $role        = clean($_POST["role"]);
      $password    = clean($_POST['password']);
      $password2   = clean($_POST['password_conf']);
      
      // Validar que no exista un usuario con ese usuario o email
      if ($db_usuario !== $usuario && usuarioModel::list(usuarioModel::$t1, ['usuario' => $usuario])) {
        throw new PDOException(sprintf('Ya existe el usuario <b>%s</b> en la base de datos.', $usuario));
      }

      // Validación del correo electrónico
      if ($db_email !== $email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new PDOException('Dirección de email no válida.');
      }

      // Validar si el correo electrónico no se ha repedito
      $sql = 'SELECT id FROM usuarios WHERE id != :id AND email = :email LIMIT 1';
      if ($db_email !== $email && usuarioModel::query($sql, ['id' => $id, 'email' => $email])) {
        throw new PDOException(sprintf('Ya existe el correo electrónico <b>%s</b> en la base de datos.', $email));
      }

      // Confirmar contraseña
      if (!empty($password) && $password !== $password2) {
        throw new PDOException('Las contraseñas no coinciden, intenta de nuevo.');
      }

      // Actualizar el registro en la base de datos
      $data =
      [
        'nombre'  => $nombre,
        'usuario' => $usuario,
        'email'   => $email,
        'role'    => $role
      ];

      // Si la contraseña ha cambiado
      if (!empty($password) && !password_verify($db_password, $password.AUTH_SALT)) {
        $data['password'] = password_hash($password.AUTH_SALT, PASSWORD_BCRYPT);
        $changed_pw       = true;
      }

      if (!usuarioModel::update(usuarioModel::$t1, ['id' => $id], $data)) {
        throw new PDOException('Hubo un problema al actualizar el registro.');
      }

      // Enviar notificación al usuario si su contraseña ha cambiado
      if ($changed_pw === true) {
        $placeholder = 
        'Datos de acceso a %s:<br>
        Usuario: <b>%s</b><br>
        Contraseña: <b>%s</b><br><br>
        No compartas esta información con nadie.<br><br>
        <a href="%s">Ingresar ahora</a>';
        $body = sprintf($placeholder, get_sitename(), $usuario, $password, URL.'login');
        send_email(get_siteemail(), $email, sprintf('Tus credenciales de acceso a %s', get_sitename()), $body, 'Ingresa al sistema con estas credenciales.');
      }

      Flasher::new(sprintf('Usuario <b>%s</b> actualizado con éxito.', $nombre), 'success');
      Redirect::back();

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

      // Limitar el acceso si es una demo del sitio
      check_if_demo();

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new PDOException('Acción no autorizada.');
      }

      if (!$usuario = usuarioModel::by_id($id)) {
        throw new PDOException('El usuario no existe en la base de datos.');
      }

      // Validar que no sea uno mismo
      if ($usuario['id'] == get_user('id')) {
        throw new PDOException('No puedes borrarte tú mismo.');
      }

      // Borrar el registro de la base de datos
      if (!usuarioModel::remove(usuarioModel::$t1, ['id' => $id], 1)) {
        throw new PDOException('Hubo un problema al borrar el registro.');
      }

      Flasher::new(sprintf('Usuario <b>%s</b> borrado con éxito.', $usuario['nombre']), 'success');
      Redirect::to('configuracion');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }
}

