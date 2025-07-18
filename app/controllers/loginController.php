<?php 

class loginController extends Controller {
  function __construct()
  {
    if (Auth::validate()) {
      Flasher::new('Ya hay una sesión abierta.');
      Redirect::to('home');
    }
  }

  function index()
  {
    $data =
    [
      'title'   => 'Ingresar a tu cuenta',
      'padding' => '0px'
    ];

    View::render('index', $data);
  }

  function post_login()
  {
    try {
      if (!Csrf::validate($_POST['csrf'])) {
        Flasher::new('Acceso no autorizado.', 'danger');
        Redirect::back();
      }
  
      $usuario  = clean($_POST['usuario']);
      $password = clean($_POST['password']);
      
      // Verificar la existencia del registro en la base de datos
      if (!$usuario = usuarioModel::list(usuarioModel::$t1, ['usuario' => $usuario], 1)) {
        throw new PDOException('Las credenciales no son correctas.');
      }

      // El usuario existe, validar contraseña
      if (!password_verify($password.AUTH_SALT, $usuario['password'])) {
        throw new PDOException('Las credenciales no son correctas.');
      }
  
      // Deprecado y no utilizado en Ordify
      $user_data = [
        'username' => 'John Doe', 
        'name'     => 'Bee Joystick', 
        'email'    => 'hellow@joystick.com.mx', 
        'avatar'   => 'myavatar.jpg', 
        'tel'      => '11223344', 
        'color'    => '#112233'
      ];
  
      // Loggear al usuario
      Auth::login($usuario['id'], $usuario);
      Flasher::new(sprintf('Bienvenido de nuevo %s', $usuario['nombre']), 'success');
      Redirect::to('home');

    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }

  }

  /**
   * Vista de reinicio de contraseña para el usuario en curso
   *
   * @return void
   */
  function reset()
  {
    $data =
    [
      'title' => 'Reiniciar contraseña'
    ];

    View::render('reset', $data);
  }

  function post_reset()
  {
    try {
      if (!Csrf::validate($_POST['csrf'])) {
        throw new Exception('Acceso no autorizado.');
      }
  
      // Validar que sea un correo electrónico válido
      $email = clean($_POST['email']);
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('La dirección de correo electrónico no es válida.');
      }

      // Validar que exista un usuario ya sea admin o regular en la base de datos
      if (!$usuario = usuarioModel::list(usuarioModel::$t1, ['email' => $email], 1)) {
        throw new PDOException('El correo electrónico no se encuentra registrado.'); // no proveer más información
      }

      // Borrar posibles tokens duplicados o ya generados
      postModel::remove(postModel::$t1, 
      [
        'tipo'       => 'password_reset_token',
        'id_usuario' => $usuario['id']
      ]);

      // Generar un token de único uso
      $token = generate_token();

      // Registrar el token en la base de datos para poder ser validado
      // ajstodo
      $post =
      [
        'tipo'       => 'password_reset_token',
        'id_usuario' => $usuario['id'],
        'titulo'     => 'Token de reinicio de contraseña',
        'status'     => 1, // 1 sin usarse 0 usado
        'mime_type'  => strtotime('+10 minutes'), // para validar hasta cuando es válido el token
        'contenido'  => $token,
        'created_at' => now()
      ];

      $id_post = postModel::add(postModel::$t1, $post);

      // Enviar correo electrónico de recuperación
      $url     = buildURL(URL.'login/reset-password', ['token' => $token, 'id' => $usuario['id']], false, false);
      $subject = sprintf('[%s] - Recuperación de contraseña', get_sitename());
      $alt     = 'Ingresa al enlace para poder restablecer tu contraseña.';
      $body    = 'Hola %s, para poder recuperar tu contraseña ingresa al siguiente enlace <a href="%s">%s</a>.<br><br>Si tú no solicitaste este cambio, recomendamos reiniciar tu contraseña, tu cuenta pudo haber sido comprometida.';
      $body    = sprintf($body, $usuario['nombre'], $url, $url);

      send_email(get_siteemail(), $usuario['email'], $subject, $body, $alt);

      // Regresar respuesta
      Flasher::new(sprintf('Hemos enviado un correo de recuperación a %s', $usuario['email']), 'success');
      Redirect::back();

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function reset_password()
  {
    try {
      // Validar que exista el token en la URL
      if (!check_get_data(['token', 'id'], $_GET)) {
        throw new Exception('Acceso no autorizado.');
      }

      // Validar que el token exista en la base de datos
      $id    = clean($_GET["id"]);
      $token = clean($_GET["token"]);

      $filters =
      [
        'tipo'       => 'password_reset_token',
        'id_usuario' => $id,
        'contenido'  => $token
      ];

      if (!$post = postModel::list(postModel::$t1, $filters, 1)) {
        throw new Exception('El token no es válido.');
      }

      // Validar que el token pasado sea aún válido o no haya expirado
      if ((int) $post['status'] !== 1 || $post['mime_type'] < time()) {
        throw new Exception('El token ingresado para restablecer tu contraseña ha expirado.');
      }

      $data =
      [
        'title' => 'Restablecimiento de contraseña',
        'p'     => $post
      ];

      View::render('resetPassword', $data);

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::to('login');
    }
  }

  function post_reset_password()
  {
    try {
      // Validar csrf
      if (!Csrf::validate($_POST["csrf"])) {
        throw new Exception('Acceso no autorizado.');
      }

      // Validar que el token exista en la base de datos
      $id    = clean($_POST["id"]);
      $token = clean($_POST["token"]);

      $filters =
      [
        'tipo'       => 'password_reset_token',
        'id_usuario' => $id,
        'contenido'  => $token
      ];

      if (!$post = postModel::list(postModel::$t1, $filters, 1)) {
        throw new Exception('El token no es válido.');
      }

      // Validar que el token pasado sea aún válido o no haya expirado
      if ((int) $post['status'] !== 1 || $post['mime_type'] < time()) {
        throw new Exception('El token ingresado para restablecer tu contraseña ha expirado.');
      }

      // Validar contraseña ingresada
      $password     = clean($_POST["password"]);
      $conf         = clean($_POST["conf_password"]);
      $uppercase    = preg_match('@[A-Z]@', $password); // debe contener 1 mayúscula / alta
      $lowercase    = preg_match('@[a-z]@', $password); // bajas
      $number       = preg_match('@[0-9]@', $password); // un número
      $specialChars = preg_match('@[^\w]@', $password); // un caracter especial
      $min          = 8;

      // Longitud
      if (strlen($password) < $min) {
        throw new Exception(sprintf('Tu contraseña es demasiado corta, debe ser mayor a %s caracteres.', $min));
      }

      // Debe contener altas
      if (!$uppercase || !$lowercase) {
        throw new Exception('La contraseña debe contener por lo menos una letrá mayúscula y minúscula.');
      }

      // Validación de digitos
      if (!$number) {
        throw new Exception('La contraseña debe contener por lo menos un número entero.');
      }

      // Validación de caracteres especiales
      if (!$specialChars) {
        throw new Exception('La contraseña debe contener por lo menos un caracter especial como $_.!?');
      }

      // Validar que sea igual a la de confirmación
      if ($password !== $conf) {
        throw new Exception('Las contraseñas no coinciden, intenta de nuevo.');
      }

      // Guardar la nueva contraseña en la base de datos
      $new_password = password_hash($password.AUTH_SALT, PASSWORD_BCRYPT);

      usuarioModel::update(usuarioModel::$t1, ['id' => $id], ['password' => $new_password]);

      // Borrar posibles tokens duplicados o ya generados
      postModel::remove(postModel::$t1, 
      [
        'tipo'       => 'password_reset_token',
        'id_usuario' => $id
      ]);

      // Información del usuario
      $usuario = usuarioModel::by_id($id);

      // Enviar email al usuario en curso para informar que se actualizó su cuenta
      $subject = sprintf('[%s] - Cambio de contraseña en tu cuenta', get_sitename());
      $alt     = 'La contraseña de tu cuenta ha sido actualizada con éxito.';
      $body    = 'Hola %s, la contraseña de tu cuenta ha sido actualizada con éxito.<br><br>Si tú no solicitaste este cambio, recomendamos reiniciar tu contraseña, tu cuenta pudo haber sido comprometida.';
      $body    = sprintf($body, $usuario['nombre']);

      send_email(get_siteemail(), $usuario['email'], $subject, $body, $alt);

      // Regresar respuesta
      Flasher::new('Tu contraseña ha sido actualizada con éxito.', 'success');
      Redirect::to('login');

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }
}