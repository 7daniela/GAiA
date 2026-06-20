<?php
// Asegurarse de que el usuario esté logueado y tengamos su ID
if (!isset($_SESSION["id"])) {
    echo '<script>window.location = "inicio";</script>';
    return;
}

$notificaciones = ControladorNotificaciones::ctrMostrarNotificaciones("user_id", $_SESSION["id"]);
?>



    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bandeja de Notificaciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
                        <li class="breadcrumb-item active">Notificaciones</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary" id="btnMarcarTodasLeidas">
                    <i class="fas fa-check-double"></i> Marcar todas como leídas
                </button>
            </div>
            <div class="card-body">

                <!-- Filters -->
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <select id="filtroEstado" class="form-control form-control-sm">
                            <option value="">Todas</option>
                            <option value="leida">Leídas</option>
                            <option value="no-leida">No leídas</option>
                        </select>
                    </div>
                    
                    <div class="col-auto ml-auto">
                        <input type="text" id="busquedaTitulo" class="form-control form-control-sm" placeholder="Buscar..." style="max-width:300px;">
                    </div>
                </div>

                <table class="table table-bordered table-striped dt-responsive tablas" width="100%">
            <thead>
            <tr>
                <th style="width:10px">#</th>
                <th>Tipo</th>
                <th>Título</th>
                <th>Mensaje</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
                    <tbody>
                        <?php
                        foreach ($notificaciones as $key => $value) {
                            echo '<tr>
                                <td>' . ($key + 1) . '</td>';
                                
                            // Icono según tipo
                            $icono = "fa-bell";
                            if($value["tipo"] == "mensaje") $icono = "fa-envelope";
                            if($value["tipo"] == "alerta") $icono = "fa-exclamation-triangle text-warning";
                            if($value["tipo"] == "sistema") $icono = "fa-cog";
                            
                            echo '<td><i class="fas '.$icono.'"></i> '.ucfirst($value["tipo"]).'</td>
                                <td>' . $value["titulo"] . '</td>
                                <td>' . $value["mensaje"] . '</td>
                                <td>' . $value["fecha"] . '</td>';
                                
                            if ($value["leido"] == 1) {
                                echo '<td><span class="badge badge-success">Leída</span></td>';
                            } else {
                                echo '<td><span class="badge badge-warning">No leída</span></td>';
                            }

                        echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <input type="hidden" id="idUsuarioGlobal" value="<?php echo $_SESSION['id']; ?>">
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->

