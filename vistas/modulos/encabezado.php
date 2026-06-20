  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" id="campanaNotificaciones">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge" id="badgeNotificaciones" style="display:none;">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header" id="headerNotificaciones">0 Notificaciones</span>
          <div class="dropdown-divider"></div>
          <div id="contenedorNotificacionesDropdown">
            <span class="dropdown-item text-center text-muted text-sm py-2">Cargando...</span>
          </div>
          <div class="dropdown-divider"></div>
          <a href="notificaciones" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
        </div>
      </li>
      
      <!-- BOTON DE SALIR TEMPORAL -->
      <div >
        <a href="Salir" class="dropdown-item"><i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión</a>
      </div>

      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>

    </ul>
    <?php if(isset($_SESSION["id"])): ?>
      <input type="hidden" id="idUsuarioGlobalEncabezado" value="<?php echo $_SESSION['id']; ?>">
    <?php endif; ?>
  </nav>
  <!-- /.navbar -->