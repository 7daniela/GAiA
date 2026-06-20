<?php

require_once "conexion.php";

class ModeloNotificaciones {

    // ************************************
    // MOSTRAR NOTIFICACIONES DE UN USUARIO
    // ************************************
    static public function mdlMostrarNotificaciones($tabla, $item, $valor, $limite = null) {
        if ($item != null) {
            $sql = "SELECT * FROM $tabla WHERE $item = :valor ORDER BY fecha DESC";
            if ($limite != null) {
                $sql .= " LIMIT $limite";
            }
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":valor", $valor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY fecha DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    // ************************************
    // CONTAR NOTIFICACIONES NO LEIDAS
    // ************************************
    static public function mdlContarNoLeidas($tabla, $item, $valor) {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM $tabla WHERE $item = :valor AND leido = 0");
        $stmt->bindParam(":valor", $valor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // ************************************
    // ACTUALIZAR ESTADO DE NOTIFICACION
    // ************************************
    static public function mdlActualizarNotificacion($tabla, $item1, $valor1, $item2, $valor2) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :valor1 WHERE $item2 = :valor2");
        $stmt->bindParam(":valor1", $valor1, PDO::PARAM_STR);
        $stmt->bindParam(":valor2", $valor2, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    // ************************************
    // MARCAR TODAS COMO LEIDAS
    // ************************************
    static public function mdlMarcarTodasLeidas($tabla, $usuario_id) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET leido = 1 WHERE user_id = :usuario_id");
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    // ************************************
    // ELIMINAR NOTIFICACION
    // ************************************
    static public function mdlEliminarNotificacion($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

}
