<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="<?php echo URL; ?>">
    <img src="<?php echo get_logo(); ?>" alt="<?php echo get_sitename(); ?>" style="width: 120px;" class="img-fluid">
  </a>
  <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <input class="form-control form-control-dark search-box w-100" type="text" name="term" placeholder="Buscar..." aria-label="Search">
  <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
      <a class="nav-link confirmar" href="logout">Salir</a>
    </li>
  </ul>
</header>

<!-- ends inc_navbar.php -->