<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de perfil
 */
class perfilController extends Controller {

  private $id   = null;
  private $user = null;

  function __construct()
  {
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }

    // Para cargar la información más actualizada
    $this->id   = get_user('id');
    $this->user = usuarioModel::by_id($this->id);
  }
  
  function index()
  {
    $this->ver($this->id);
  }

  function ver()
  {
    $data = 
    [
      'title' => 'Mi perfil',
      'slug'  => 'perfil',
      'u'     => $this->user
    ];

    View::render('perfil', $data);
  }

  function post_editar()
  {
    try {
      if (!Csrf::validate($_POST['csrf']) || !check_posted_data(['nombre','usuario','email','password','password_conf','csrf'], $_POST)) {
        throw new Exception('Acceso no autorizado.');
      }

      // Limitar el acceso si es una demo del sitio
      check_if_demo();

      // Validar la existencia del registro
      $id = $this->id;
      if (!$u = usuarioModel::by_id($id)) {
        throw new Exception('No existe el usuario en la base de datos.');
      }

      $is_admin    = is_admin();
      $db_nombre   = $u['nombre'];
      $db_usuario  = $u['usuario'];
      $db_email    = $u['email'];
      $db_password = $u['password'];
      $changed_pw  = false;

      $nombre      = clean($_POST['nombre']);
      $usuario     = clean($_POST['usuario']);
      $email       = clean($_POST['email']);
      $password    = clean($_POST['password']);
      $password2   = clean($_POST['password_conf']);

      // Solo si es administrador puede editar role | email | usuario
      if ($is_admin) {
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
      }

      // Confirmar contraseña
      if (!empty($password) && $password !== $password2) {
        throw new PDOException('Las contraseñas no coinciden, intenta de nuevo.');
      }

      // Actualizar el registro en la base de datos
      $data =
      [
        'nombre'  => $nombre,
        'usuario' => $is_admin ? $usuario : $db_usuario,
        'email'   => $is_admin ? $email : $db_email
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

      Flasher::new('Cambios guardados con éxito.', 'success');
      Redirect::back();

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }
}

