<?php

require_once "../controladores/notificaciones.controlador.php";
require_once "../modelos/notificaciones.modelo.php";

class AjaxNotificaciones {

    public $idUsuario;
    public $idNotificacion;
    public $accion;

    // ************************************
    // OBTENER NOTIFICACIONES Y CONTADOR
    // ************************************
    public function ajaxObtenerNotificaciones() {
        $item = "user_id";
        $valor = $this->idUsuario;

        $notificaciones = ControladorNotificaciones::ctrMostrarNotificaciones($item, $valor, 5); // Ultimas 5
        $noLeidas = ControladorNotificaciones::ctrContarNoLeidas($item, $valor);

        $datos = array(
            "notificaciones" => $notificaciones,
            "noLeidas" => $noLeidas["total"]
        );

        echo json_encode($datos);
    }

    // ************************************
    // MARCAR COMO LEIDA / NO LEIDA
    // ************************************
    public function ajaxMarcarLeida() {
        $item1 = "leido";
        $valor1 = $this->accion == "leer" ? 1 : 0;
        $item2 = "id";
        $valor2 = $this->idNotificacion;

        $respuesta = ControladorNotificaciones::ctrActualizarNotificacion($item1, $valor1, $item2, $valor2);
        echo json_encode($respuesta);
    }

    // ************************************
    // MARCAR TODAS COMO LEIDAS
    // ************************************
    public function ajaxMarcarTodasLeidas() {
        $respuesta = ControladorNotificaciones::ctrMarcarTodasLeidas($this->idUsuario);
        echo json_encode($respuesta);
    }

    // ************************************
    // ELIMINAR NOTIFICACION
    // ************************************
    public function ajaxEliminarNotificacion() {
        $respuesta = ControladorNotificaciones::ctrEliminarNotificacion($this->idNotificacion);
        echo json_encode($respuesta);
    }
}

// ************************************
// ACCIONES
// ************************************

// Obtener Notificaciones (Dropdown Campana)
if (isset($_POST["obtenerNotificaciones"])) {
    $notificaciones = new AjaxNotificaciones();
    $notificaciones->idUsuario = $_POST["idUsuario"];
    $notificaciones->ajaxObtenerNotificaciones();
}

// Marcar como leída o no leída
if (isset($_POST["marcarLeida"])) {
    $notificaciones = new AjaxNotificaciones();
    $notificaciones->idNotificacion = $_POST["idNotificacion"];
    $notificaciones->accion = $_POST["accion"]; // "leer" o "noleer"
    $notificaciones->ajaxMarcarLeida();
}

// Marcar todas como leídas
if (isset($_POST["marcarTodasLeidas"])) {
    $notificaciones = new AjaxNotificaciones();
    $notificaciones->idUsuario = $_POST["idUsuario"];
    $notificaciones->ajaxMarcarTodasLeidas();
}

// Eliminar notificación
if (isset($_POST["eliminarNotificacion"])) {
    $notificaciones = new AjaxNotificaciones();
    $notificaciones->idNotificacion = $_POST["idNotificacion"];
    $notificaciones->ajaxEliminarNotificacion();
}

