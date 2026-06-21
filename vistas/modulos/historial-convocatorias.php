<?php

if (!ControladorHistorialConvocatorias::ctrUsuarioPuedeVerHistorial()) {
    include __DIR__ . "/error404.php";
    return;
}

function hc($valor)
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

function hcFecha($valor)
{
    if (empty($valor)) {
        return "Sin registro";
    }

    return date("d/m/Y", strtotime($valor));
}

function hcMoneda($valor)
{
    if ($valor === null || $valor === "") {
        return "Sin registro";
    }

    return "$" . number_format((float)$valor, 0, ",", ".");
}

$filtros = array(
    "vigencia" => isset($_GET["vigencia"]) ? trim($_GET["vigencia"]) : "",
    "tipo_apoyo" => isset($_GET["tipo_apoyo"]) ? trim($_GET["tipo_apoyo"]) : ""
);

$paginaActual = isset($_GET["pagina"]) ? max(1, (int)$_GET["pagina"]) : 1;
$porPagina = 10;

$vigencias = ControladorHistorialConvocatorias::ctrListarVigencias();
$tiposApoyo = ControladorHistorialConvocatorias::ctrListarTiposApoyo();
$totalConvocatoriasCreadas = ControladorHistorialConvocatorias::ctrContarConvocatoriasCreadas($filtros);
$convocatoriasCreadas = ControladorHistorialConvocatorias::ctrListarConvocatoriasCreadas($paginaActual, 10, $filtros);
$totalRegistros = ControladorHistorialConvocatorias::ctrContarHistorial($filtros);
$totalPaginas = max(1, (int)ceil($totalRegistros / $porPagina));

if ($paginaActual > $totalPaginas) {
    $paginaActual = $totalPaginas;
}

$historial = ControladorHistorialConvocatorias::ctrListarHistorial($filtros, $paginaActual, $porPagina);

function hcUrlPagina($pagina)
{
    $params = $_GET;
    $params["ruta"] = "historial-convocatorias";
    $params["pagina"] = $pagina;
    return "index.php?" . http_build_query($params);
}
?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Historial de Convocatorias Cerradas y Creadas</h1>
            </div>
            <div class="col-sm-6">
                <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
                        <li class="breadcrumb-item active">Historial</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content" id="vista-historial-convocatorias">
    <div class="container-fluid">
        <div>
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-cerradas-tab" data-toggle="tab" href="#tab-cerradas" role="tab" aria-controls="tab-cerradas" aria-selected="true">Convocatorias cerradas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-creadas-tab" data-toggle="tab" href="#tab-creadas" role="tab" aria-controls="tab-creadas" aria-selected="false">Convocatorias creadas</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-cerradas" role="tabpanel" aria-labelledby="tab-cerradas-tab">
                    <div class="card bg-dark text-white">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold mb-0">CONVOCATORIAS CERRADAS</h3>
                        </div>
                        <div class="card-body">
                            <form method="get" action="index.php" class="historial-filtros mb-4">
                                <input type="hidden" name="ruta" value="historial-convocatorias">
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <label for="filtroVigencia">A&ntilde;o o vigencia</label>
                                        <select class="form-control" id="filtroVigencia" name="vigencia">
                                            <option value="">Todas</option>
                                            <?php foreach ($vigencias as $vigencia): ?>
                                                <option value="<?php echo hc($vigencia["vigencia_fiscal"]); ?>" <?php echo ($filtros["vigencia"] == $vigencia["vigencia_fiscal"]) ? "selected" : ""; ?> >
                                                    <?php echo hc($vigencia["vigencia_fiscal"]); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label for="filtroTipoApoyo">Tipo de apoyo</label>
                                        <select class="form-control" id="filtroTipoApoyo" name="tipo_apoyo">
                                            <option value="">Todos</option>
                                            <?php foreach ($tiposApoyo as $tipo): ?>
                                                <option value="<?php echo hc($tipo["tipo_apoyo"]); ?>" <?php echo ($filtros["tipo_apoyo"] === $tipo["tipo_apoyo"]) ? "selected" : ""; ?>>
                                                    <?php echo hc($tipo["tipo_apoyo"]); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-6 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-filter mr-1"></i> Filtrar
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <?php if (empty($historial)): ?>
                                <div class="alert alert-secondary mb-0">
                                    No existen convocatorias cerradas para los filtros seleccionados.
                                </div>
                            <?php else: ?>
                                <div class="historial-timeline">
                                    <?php foreach ($historial as $convocatoria): ?>
                                        <article class="historial-card">
                                            <div class="historial-card-marker">
                                                <i class="fas fa-lock"></i>
                                            </div>
                                            <div class="historial-card-content">
                                                <div class="historial-card-header">
                                                    <div>
                                                        <span class="historial-id">ID #<?php echo hc($convocatoria["id_convocatoria_original"]); ?></span>
                                                        <h4><?php echo hc($convocatoria["tipo_apoyo"]); ?></h4>
                                                    </div>
                                                    <span class="badge badge-success">Cerrada</span>
                                                </div>

                                                <div class="row historial-metricas">
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Presupuesto comprometido</span>
                                                        <strong><?php echo hcMoneda($convocatoria["presupuesto_comprometido"]); ?></strong>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Cupos</span>
                                                        <strong><?php echo hc($convocatoria["cupos_ofertados"]); ?> / <?php echo hc($convocatoria["cupos_adjudicados"]); ?></strong>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Fecha apertura</span>
                                                        <strong><?php echo hcFecha($convocatoria["fecha_apertura"]); ?></strong>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Fecha cierre</span>
                                                        <strong><?php echo hcFecha($convocatoria["fecha_cierre_definitivo"]); ?></strong>
                                                    </div>
                                                </div>

                                                <div class="historial-cierre">
                                                    <span>Cierre por:</span>
                                                    <strong><?php echo trim($convocatoria["usuario_cierre"]) !== "" ? hc($convocatoria["usuario_cierre"]) : "Usuario no registrado"; ?></strong>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($totalPaginas > 1): ?>
                                    <nav aria-label="Paginacion historial" class="mt-4">
                                        <ul class="pagination justify-content-center">
                                            <li class="page-item <?php echo $paginaActual <= 1 ? "disabled" : ""; ?>">
                                                <a class="page-link" href="<?php echo hcUrlPagina($paginaActual - 1); ?>">Anterior</a>
                                            </li>
                                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                                <li class="page-item <?php echo $i == $paginaActual ? "active" : ""; ?>">
                                                    <a class="page-link" href="<?php echo hcUrlPagina($i); ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <li class="page-item <?php echo $paginaActual >= $totalPaginas ? "disabled" : ""; ?>">
                                                <a class="page-link" href="<?php echo hcUrlPagina($paginaActual + 1); ?>">Siguiente</a>
                                            </li>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-creadas" role="tabpanel" aria-labelledby="tab-creadas-tab">
                    <div class="card bg-dark text-white mb-4">
                        <div class="card-header border-0">
                            <h3 class="card-title font-weight-bold mb-0">CONVOCATORIAS CREADAS</h3>
                        </div>
                        <div class="card-body">
                            <form method="get" action="index.php" class="historial-filtros mb-4">
                                <input type="hidden" name="ruta" value="historial-convocatorias">
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <label for="filtroVigenciaC">A&ntilde;o o vigencia</label>
                                        <select class="form-control" id="filtroVigenciaC" name="vigencia">
                                            <option value="">Todas</option>
                                            <?php foreach ($vigencias as $vigencia): ?>
                                                <option value="<?php echo hc($vigencia["vigencia_fiscal"]); ?>" <?php echo ($filtros["vigencia"] == $vigencia["vigencia_fiscal"]) ? "selected" : ""; ?>>
                                                    <?php echo hc($vigencia["vigencia_fiscal"]); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label for="filtroTipoApoyoC">Tipo de apoyo</label>
                                        <select class="form-control" id="filtroTipoApoyoC" name="tipo_apoyo">
                                            <option value="">Todos</option>
                                            <?php foreach ($tiposApoyo as $tipo): ?>
                                                <option value="<?php echo hc($tipo["tipo_apoyo"]); ?>" <?php echo ($filtros["tipo_apoyo"] === $tipo["tipo_apoyo"]) ? "selected" : ""; ?>>
                                                    <?php echo hc($tipo["tipo_apoyo"]); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-6 mb-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-filter mr-1"></i> Filtrar
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="mb-3 text-white-50">
                                Total creadas: <strong><?php echo hc($totalConvocatoriasCreadas); ?></strong>
                            </div>

                            <?php if (empty($convocatoriasCreadas)): ?>
                                <div class="alert alert-secondary mb-4">
                                    No existen convocatorias creadas para mostrar.
                                </div>
                            <?php else: ?>
                                <div class="historial-timeline mb-4">
                                    <?php foreach ($convocatoriasCreadas as $convocatoria): ?>
                                        <article class="historial-card">
                                            <div class="historial-card-marker">
                                                <i class="fas fa-plus-circle"></i>
                                            </div>
                                            <div class="historial-card-content">
                                                <div class="historial-card-header">
                                                    <div>
                                                        <span class="historial-id">ID #<?php echo hc($convocatoria["id"] ?? $convocatoria["id_convocatoria_original"] ?? "-"); ?></span>
                                                        <h4><?php echo hc($convocatoria["tipo_apoyo"] ?? "Sin tipo"); ?></h4>
                                                    </div>
                                                    <span class="badge badge-info">Creada</span>
                                                </div>

                                                <div class="row historial-metricas">
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Presupuesto comprometido</span>
                                                        <strong><?php echo hcMoneda($convocatoria["presupuesto_comprometido"] ?? null); ?></strong>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Cupos</span>
                                                        <strong><?php echo hc($convocatoria["cupos_ofertados"] ?? "-"); ?> / <?php echo hc($convocatoria["cupos_adjudicados"] ?? "-"); ?></strong>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Fecha apertura</span>
                                                        <strong><?php echo hcFecha($convocatoria["fecha_apertura"] ?? ""); ?></strong>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 mb-3">
                                                        <span>Fecha cierre</span>
                                                        <strong><?php echo hcFecha($convocatoria["fecha_cierre_definitivo"] ?? ""); ?></strong>
                                                    </div>
                                                </div>

                                                <div class="historial-cierre">
                                                    <span>Creado por:</span>
                                                    <strong><?php echo trim($convocatoria["usuario_creador"] ?? "") !== "" ? hc($convocatoria["usuario_creador"]) : "Usuario no registrado"; ?></strong>
                                                </div>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
