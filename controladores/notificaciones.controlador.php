<?php

class ControladorNotificaciones {

    // ************************************
    // MOSTRAR NOTIFICACIONES
    // ************************************
    static public function ctrMostrarNotificaciones($item, $valor, $limite = null) {
        $tabla = "notificaciones";
        $respuesta = ModeloNotificaciones::mdlMostrarNotificaciones($tabla, $item, $valor, $limite);
        return $respuesta;
    }

    // ************************************
    // CONTAR NO LEIDAS
    // ************************************
    static public function ctrContarNoLeidas($item, $valor) {
        $tabla = "notificaciones";
        $respuesta = ModeloNotificaciones::mdlContarNoLeidas($tabla, $item, $valor);
        return $respuesta;
    }

    // ************************************
    // ACTUALIZAR NOTIFICACION
    // ************************************
    static public function ctrActualizarNotificacion($item1, $valor1, $item2, $valor2) {
        $tabla = "notificaciones";
        $respuesta = ModeloNotificaciones::mdlActualizarNotificacion($tabla, $item1, $valor1, $item2, $valor2);
        return $respuesta;
    }

    // ************************************
    // MARCAR TODAS COMO LEIDAS
    // ************************************
    static public function ctrMarcarTodasLeidas($usuario_id) {
        $tabla = "notificaciones";
        $respuesta = ModeloNotificaciones::mdlMarcarTodasLeidas($tabla, $usuario_id);
        return $respuesta;
    }

    // ************************************
    // ELIMINAR NOTIFICACION
    // ************************************
    static public function ctrEliminarNotificacion($id) {
        $tabla = "notificaciones";
        $respuesta = ModeloNotificaciones::mdlEliminarNotificacion($tabla, $id);
        return $respuesta;
    }
}
