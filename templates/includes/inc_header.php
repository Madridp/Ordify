<!DOCTYPE html>
<html lang="<?php echo SITE_LANG; ?>">
<head>
  <!-- Agregar basepath para definir a partir de donde se deben generar los enlaces y la carga de archivos -->
  <base href="<?php echo BASEPATH; ?>">
  <title><?php echo isset($d->title) ? $d->title.' - '.get_sitename() : 'Bienvenido - '.get_sitename(); ?></title>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Administra pedidos a proveedores y compras de forma sencilla para tu negocio.">

  <!-- Favicon de Ordify -->
  <?php echo get_favicon(); ?>

  <!-- Bootstrap core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Toastr css -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

  <!-- Waitme css -->
  <link rel="stylesheet" href="<?php echo PLUGINS.'waitme/waitMe.min.css'; ?>">

  <!-- Font awesome 5 -->
  <script src="https://kit.fontawesome.com/eef3c81fe6.js" crossorigin="anonymous"></script>

  <!-- Lightbox2 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
  
  <!-- Custom styles for this template -->
  <link href="<?php echo CSS.'dashboard.css'; ?>" rel="stylesheet">

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      user-select: none;
    }

    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }
  </style>
</head>
<body>
<!-- ends inc_header.php -->