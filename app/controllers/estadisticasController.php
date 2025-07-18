<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de estadisticas
 */
class estadisticasController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }

    // Bloquear el acceso a todas las rutas de configuración
    if (!is_admin()) {
      Flasher::deny();
      Redirect::to('home');
    }
  }
  
  function index()
  {
    $data =
    [
      'title'    => 'Estadísticas',
      'slug'     => 'estadisticas'
    ];
    
    View::render('index', $data);
  }
}

