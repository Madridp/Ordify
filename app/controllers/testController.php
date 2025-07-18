<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de test
 */
class testController extends Controller {
  function __construct()
  {
    // Validación de sesión de usuario, descomentar si requerida
    /**
    if (!Auth::validate()) {
      Flasher::new('Debes iniciar sesión primero.', 'danger');
      Redirect::to('login');
    }
    */
  }
  
  function index()
  {
    send_email_nuevo_pedido(1);
  }
}

