<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de configuracion
 */
class configuracionController extends Controller {
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
      'title'    => 'Configuración',
      'slug'     => 'configuracion',
      'usuarios' => usuarioModel::all_paginated()
    ];
    
    View::render('index', $data);
  }

  function update()
  {
    try {
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])) {
        Flasher::deny();
        Redirect::back();
      }
      
      // Nuevo sistema para validar la versión y si es requerido realizar
      // alguna actualización de base de datos
      $version = get_version();
      if (version_compare($version, '1.2.0', '<=')) {
        // Query de actualización
        $sql = 'ALTER TABLE usuarios ADD COLUMN role VARCHAR(20) AFTER id';
        if (!usuarioModel::query($sql)) {
          throw new Exception('Hubo un problema al actualizar la base de datos.');
        }

        // Actualizado con éxito
        Flasher::new('Se ha agradado la columna <b>"role"</b> a la tabla "usuarios" con éxito.', 'success');
        Redirect::back();
      
      } else if (version_compare($version, '1.2.0', '>') && version_compare($version, '1.3.0', '<=')) {
        // Query de actualización
        $sql = 'ALTER TABLE pedidos
        ADD COLUMN metodo_pago VARCHAR(30) AFTER notas,
        ADD COLUMN status_pago VARCHAR(20) AFTER metodo_pago,
        ADD COLUMN fecha_pago DATETIME NULL AFTER status_pago,
        ADD COLUMN total_pagado FLOAT(10, 2) DEFAULT 0 AFTER fecha_pago';
        
        if (!pedidoModel::query($sql)) {
          throw new Exception('Hubo un problema al actualizar la base de datos.');
        }

        // Actualizado con éxito
        Flasher::new('Se han agradado 3 columnas a la tabla "pedidos" con éxito.', 'success');
        Redirect::back();

      } else {
        Flasher::deny();
        Redirect::back();
      }

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }

  function generar_skus()
  {
    try {
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])) {
        Flasher::deny();
        Redirect::back();
      }

      // Solo permitir acción al superusuario
      if (!is_admin()) {
        throw new Exception('Acción no autorizada para el usuario.');
      }

      $productos = productoModel::all();

      if (empty($productos)) {
        throw new Exception('No hay productos en la base de datos.');
      }

      $sku       = null;
      $generated = 0;
      $errors    = 0;
      foreach ($productos as $p) {
        if (!empty($p['sku'])) {
          continue;
        }

        $sku = random_password(10, 'numeric');
        if (!productoModel::update(productoModel::$t1, ['id' => $p['id']], ['sku' => $sku])) {
          $errors++;
          continue;
        }

        $generated++;
      }

      // Actualizado con éxito
      if ($errors > 0) {
        Flasher::new(sprintf('Hubo errores en la generación de SKUs: %s', $errors), 'danger');
      }

      if ($generated > 0) {
        Flasher::new(sprintf('Generamos <b>%s SKUs</b> nuevos para productos.', $generated), 'success');
      }

      Redirect::back();

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }
}

