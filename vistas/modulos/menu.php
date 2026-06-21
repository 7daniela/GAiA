   <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <!-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <span class="brand-text font-weight-light">GAiA</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="vistas/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">
            <?php
              $nombreSesion = trim(($_SESSION["nombres"] ?? "") . " " . ($_SESSION["apellidos"] ?? ""));
              echo $nombreSesion !== "" ? htmlspecialchars($nombreSesion, ENT_QUOTES, "UTF-8") : "Usuario";
            ?>
          </a>
        </div>
      </div>


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

           <!-- CONFIGURACION -->
          <li class="nav-item menu-open ">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Configuracion
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="apoyos" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Apoyos</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="./sedes" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sedes</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="Usuarios" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Usuarios</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="fichas" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Fichas</p>
                </a>
              </li>              

            </ul>
          </li>


          <li class="nav-item">
            <a href="identificacion" class="nav-link">
              <i class="nav-icon fas fa-life-ring"></i>
              <p>
                Identificación de apoyos
              </p>
            </a>
          </li>


          <li class="nav-item">
            <a href="inscripciones" class="nav-link">
              <i class="nav-icon fas fa-pencil-alt"></i>
              <p>
                Inscripciones
              </p>
            </a>
          </li> 
          
          <li class="nav-item menu-open">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-handshake"></i>
              <p>
                Gestión
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="verificacion" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Verificación</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="puntuacion" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Puntuación</p>
                </a>
              </li>

            </ul>
          </li>   
          
          <li class="nav-item">
            <a href="financiera" class="nav-link">
              <i class="fas fa-money-bill-wave"></i>
              <p>
                Financiera
              </p>
            </a>
          </li>
          
          <li class="nav-item">
            <a href="reportes" class="nav-link">
              <i class="fas fa-chart-bar"></i>
              <p>
                Reportes
              </p>
            </a>
          </li>          

          <?php if (class_exists("ControladorHistorialConvocatorias") && ControladorHistorialConvocatorias::ctrUsuarioPuedeVerHistorial()): ?>
          <li class="nav-item">
            <a href="historial-convocatorias" class="nav-link">
              <i class="fas fa-history nav-icon"></i>
              <p>
                Historial de convocatorias
              </p>
            </a>
          </li>
          <?php endif; ?>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside> 
