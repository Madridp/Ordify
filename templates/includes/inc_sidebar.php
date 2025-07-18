<div class="container-fluid pt-2 pb-5">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <?php echo create_menu([
          ['slug' => 'home', 'title' => 'Dashboard', 'url' => URL.'home', 'icon' => 'home'],
          ['slug' => 'perfil', 'title' => 'Mi perfil', 'url' => URL.'perfil', 'icon' => 'user'],
          ['slug' => 'pedidos', 'title' => 'Pedidos', 'url' => URL.'pedidos', 'icon' => 'file'],
          ['slug' => 'proveedores', 'title' => 'Proveedores', 'url' => URL.'proveedores', 'icon' => 'shopping-cart'],
          ['slug' => 'productos', 'title' => 'Productos', 'url' => URL.'productos', 'icon' => 'layers'],
          ['slug' => 'usuarios', 'title' => 'Usuarios', 'url' => URL.'usuarios', 'icon' => 'users', 'admins' => true],
          ['slug' => 'estadisticas', 'title' => 'Estadísticas', 'url' => URL.'estadisticas', 'icon' => 'image', 'admins' => true],
          ['slug' => 'configuracion', 'title' => 'Configuración', 'url' => URL.'configuracion', 'icon' => 'settings', 'admins' => true],
        ], !isset($d->slug) ? strtolower($d->title) : $d->slug); ?>
      </div>
    </nav>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

    <div class="row">
      <div class="col-12">
        <?php echo Flasher::flash(); ?>
      </div>
    </div>

    <div class="main_wrapper_content">
      <div class="wrapper_dynamic_content"></div>
    <!-- ends inc_sidebar.php -->