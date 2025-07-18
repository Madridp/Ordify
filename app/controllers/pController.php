<?php

/**
 * Plantilla general de controladores
 * Versión 1.0.0
 *
 * Controlador de p
 */
class pController extends Controller {
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
    echo 'No hay nada por aquí.';
  }

  function pedido($hash)
  {
    if (!$pedido = pedidoModel::list(pedidoModel::$t1, ['hash' => $hash], 1)) {
      die('No existe el pedido que estás buscando.');
    }
    $pedido = pedidoModel::by_id($pedido['id']);
    
    // Validar que el estado del pedido sea válido para acceder
    if ($pedido['status'] === 'borrador' && !Auth::validate()) {
      die('Todavía no es posible mostrar este pedido, intenta más tarde.');
    }

    $data =
    [
      'title' => sprintf('Pedido #%s', $pedido['numero']),
      'p'     => $pedido,
      'url'   => sprintf(URL.'p/pedido/%s', $pedido['hash'])
    ];

    View::render('ver', $data);
  }

  function export($id)
  {
    try {
      if (!check_get_data(['_t'], $_GET) || !Csrf::validate($_GET['_t'])) {
        throw new Exception('Acceso no autorizado.');
      }

      if (!$pedido = pedidoModel::by_id($id)) {
        throw new Exception('El pedido no existe en la base de datos.');
      }

      $productos = $pedido['productos'];

      if (empty($productos)) {
        throw new Exception('No hay productos en el pedido para exportar.');
      }

      $filename   = sprintf('Exported_csv_%s.csv', $pedido['numero']);

      // Descargando
      header('Content-Encoding: UTF-8');
      header('Content-type: text/csv; charset=UTF-8');
      header('Content-Disposition: attachment; filename="'.$filename.'"');
      echo "\xEF\xBB\xBF"; // UTF-8 BOM
      
      // Insertando la información en el csv
      $fp = fopen('php://output', 'wb');

      // Cabeceras
      fputcsv($fp, ['Producto', 'Corte', 'Cantidad', 'Precio', 'Subtotal', 'Total']);

      // Filas
      foreach ( $productos as $p ) {
        $row =
        [
          $p['nombre'],
          $p['corte'],
          $p['cantidad'],
          money($p['precio']),
          money($p['subtotal']),
          money($p['total'])
        ];
        fputcsv($fp, $row);
      }

      // Espaciado
      fputcsv($fp, []);

      // Resumen del pedido
      fputcsv($fp, [sprintf('Resumen pedido #%s', $pedido['numero']), 'Piezas Totales', $pedido['total_cantidades'], '', 'Total', money($pedido['total'])]);

      // Espaciado
      fputcsv($fp, []);

      // Notas
      fputcsv($fp, ['Notas']);
      fputcsv($fp, [$pedido['notas']]);

      fclose($fp);
      return true;

    } catch (Exception $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    } catch (PDOException $e) {
      Flasher::new($e->getMessage(), 'danger');
      Redirect::back();
    }
  }
}

