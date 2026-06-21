<?php

require_once "conexion.php";

class ModeloHistorialConvocatorias
{
    static private function construirWhere($filtros, &$params)
    {
        $where = array("1=1");

        if (!empty($filtros["vigencia"])) {
            $where[] = "h.vigencia_fiscal = :vigencia";
            $params[":vigencia"] = array($filtros["vigencia"], PDO::PARAM_INT);
        }

        if (!empty($filtros["tipo_apoyo"])) {
            $where[] = "h.tipo_apoyo = :tipo_apoyo";
            $params[":tipo_apoyo"] = array($filtros["tipo_apoyo"], PDO::PARAM_STR);
        }

        return implode(" AND ", $where);
    }

    static private function enlazarParametros($stmt, $params)
    {
        foreach ($params as $nombre => $parametro) {
            $stmt->bindValue($nombre, $parametro[0], $parametro[1]);
        }
    }

    static public function mdlListarHistorial($filtros, $inicio, $limite)
    {
        $params = array();
        $where = self::construirWhere($filtros, $params);

        $sql = "SELECT
                    h.id_historico,
                    h.id_convocatoria_original,
                    h.vigencia_fiscal,
                    h.tipo_apoyo,
                    h.presupuesto_comprometido,
                    h.cupos_ofertados,
                    h.cupos_adjudicados,
                    h.fecha_apertura,
                    h.fecha_cierre_definitivo,
                    h.usuario_cierre_id,
                    TRIM(CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, ''))) AS usuario_cierre
                FROM historial_convocatorias h
                LEFT JOIN usuarios u ON u.id = h.usuario_cierre_id
                WHERE $where
                ORDER BY h.fecha_cierre_definitivo DESC, h.id_historico DESC
                LIMIT :inicio, :limite";

        $stmt = Conexion::conectar()->prepare($sql);
        self::enlazarParametros($stmt, $params);
        $stmt->bindValue(":inicio", (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlContarHistorial($filtros)
    {
        $params = array();
        $where = self::construirWhere($filtros, $params);

        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM historial_convocatorias h WHERE $where");
        self::enlazarParametros($stmt, $params);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    static public function mdlContarConvocatoriasCreadas()
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM convocatorias");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    static public function mdlContarConvocatoriasCreadasConFiltro($filtros)
    {
        $where = array("1=1");
        $params = array();

        if (!empty($filtros["vigencia"])) {
            $where[] = "YEAR(c.fecha_fin) = :vigencia";
            $params[":vigencia"] = array($filtros["vigencia"], PDO::PARAM_INT);
        }

        if (!empty($filtros["tipo_apoyo"])) {
            $where[] = "a.descripcion_apoyo = :tipo_apoyo";
            $params[":tipo_apoyo"] = array($filtros["tipo_apoyo"], PDO::PARAM_STR);
        }

        $sql = "SELECT COUNT(*) FROM convocatorias c LEFT JOIN apoyos a ON a.id_apoyo = c.apoyo_id WHERE " . implode(" AND ", $where);
        $stmt = Conexion::conectar()->prepare($sql);
        foreach ($params as $nombre => $parametro) {
            $stmt->bindValue($nombre, $parametro[0], $parametro[1]);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    static public function mdlListarConvocatoriasCreadas($inicio, $limite, $filtros = array())
    {
        $where = array("1=1");
        $params = array();

        if (!empty($filtros["vigencia"])) {
            $where[] = "YEAR(c.fecha_fin) = :vigencia";
            $params[":vigencia"] = array($filtros["vigencia"], PDO::PARAM_INT);
        }

        if (!empty($filtros["tipo_apoyo"])) {
            $where[] = "a.descripcion_apoyo = :tipo_apoyo";
            $params[":tipo_apoyo"] = array($filtros["tipo_apoyo"], PDO::PARAM_STR);
        }

        $sql = "SELECT c.*, a.descripcion_apoyo AS tipo_apoyo
                FROM convocatorias c
                LEFT JOIN apoyos a ON a.id_apoyo = c.apoyo_id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY c.id DESC
                LIMIT :inicio, :limite";

        $stmt = Conexion::conectar()->prepare($sql);
        foreach ($params as $nombre => $parametro) {
            $stmt->bindValue($nombre, $parametro[0], $parametro[1]);
        }
        $stmt->bindValue(":inicio", (int)$inicio, PDO::PARAM_INT);
        $stmt->bindValue(":limite", (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlListarVigencias()
    {
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT vigencia_fiscal FROM historial_convocatorias ORDER BY vigencia_fiscal DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlContarCuposAdjudicados($idConvocatoria)
    {
        $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) FROM inscripciones WHERE convocatoria_id = :id AND estado = 'SELECCIONADO'");
        $stmt->bindValue(":id", (int)$idConvocatoria, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    static public function mdlListarTiposApoyo()
    {
        $stmt = Conexion::conectar()->prepare("SELECT DISTINCT tipo_apoyo FROM historial_convocatorias WHERE tipo_apoyo IS NOT NULL AND tipo_apoyo <> '' ORDER BY tipo_apoyo ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlListarSedesHistorial()
    {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM sedes ORDER BY descripcion_sede ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static public function mdlRegistrarCierreConvocatoria($idConvocatoria, $idUsuario)
    {
        $conexion = Conexion::conectar();
        $conexion->beginTransaction();

        try {
            $stmt = $conexion->prepare("SELECT c.*, a.descripcion_apoyo
                FROM convocatorias c
                LEFT JOIN apoyos a ON a.id_apoyo = c.apoyo_id
                WHERE c.id = :id
                FOR UPDATE");
            $stmt->bindValue(":id", $idConvocatoria, PDO::PARAM_INT);
            $stmt->execute();
            $convocatoria = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$convocatoria) {
                $conexion->rollBack();
                return "no_encontrada";
            }

            if ($convocatoria["estado_en_convocatoria"] === "CERRADA") {
                $stmtHistorial = $conexion->prepare("SELECT COUNT(*) FROM historial_convocatorias WHERE id_convocatoria_original = :id");
                $stmtHistorial->bindValue(":id", $idConvocatoria, PDO::PARAM_INT);
                $stmtHistorial->execute();

                if ((int)$stmtHistorial->fetchColumn() > 0) {
                    $conexion->rollBack();
                    return "ya_cerrada";
                }
            }

            $stmtUsuario = $conexion->prepare("SELECT id, nombres, apellidos, rol FROM usuarios WHERE id = :id");
            $stmtUsuario->bindValue(":id", $idUsuario, PDO::PARAM_INT);
            $stmtUsuario->execute();
            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                $conexion->rollBack();
                return "usuario_no_encontrado";
            }

            $stmtCupos = $conexion->prepare("SELECT COUNT(*) FROM inscripciones WHERE convocatoria_id = :id AND estado = 'SELECCIONADO'");
            $stmtCupos->bindValue(":id", $idConvocatoria, PDO::PARAM_INT);
            $stmtCupos->execute();
            $cuposAdjudicados = (int)$stmtCupos->fetchColumn();

            $fechaCierre = date("Y-m-d H:i:s");
            $vigencia = (int)date("Y", strtotime($convocatoria["fecha_fin"] ?: $fechaCierre));
            $usuarioNombre = trim($usuario["nombres"] . " " . $usuario["apellidos"]);
            $snapshot = array(
                "convocatoria" => $convocatoria,
                "cupos_adjudicados" => $cuposAdjudicados,
                "usuario_cierre" => array(
                    "id" => $usuario["id"],
                    "nombres" => $usuario["nombres"],
                    "apellidos" => $usuario["apellidos"],
                    "rol" => $usuario["rol"]
                )
            );
            $snapshotJson = json_encode($snapshot, JSON_UNESCAPED_UNICODE);
            $hash = hash("sha256", $snapshotJson);

            $stmtInsert = $conexion->prepare("INSERT INTO historial_convocatorias
                (id_convocatoria_original, vigencia_fiscal, tipo_apoyo, presupuesto_comprometido, cupos_ofertados, cupos_adjudicados, fecha_apertura, fecha_cierre_definitivo, usuario_cierre_id, usuario_nombre, snapshot_json, hash_integridad)
                VALUES
                (:id_convocatoria_original, :vigencia_fiscal, :tipo_apoyo, NULL, :cupos_ofertados, :cupos_adjudicados, :fecha_apertura, :fecha_cierre_definitivo, :usuario_cierre_id, :usuario_nombre, :snapshot_json, :hash_integridad)");

            $stmtInsert->bindValue(":id_convocatoria_original", $idConvocatoria, PDO::PARAM_INT);
            $stmtInsert->bindValue(":vigencia_fiscal", $vigencia, PDO::PARAM_INT);
            $stmtInsert->bindValue(":tipo_apoyo", $convocatoria["descripcion_apoyo"], PDO::PARAM_STR);
            $stmtInsert->bindValue(":cupos_ofertados", $convocatoria["cupos_personas"], PDO::PARAM_INT);
            $stmtInsert->bindValue(":cupos_adjudicados", $cuposAdjudicados, PDO::PARAM_INT);
            $stmtInsert->bindValue(":fecha_apertura", $convocatoria["fecha_inicio"] . " 00:00:00", PDO::PARAM_STR);
            $stmtInsert->bindValue(":fecha_cierre_definitivo", $fechaCierre, PDO::PARAM_STR);
            $stmtInsert->bindValue(":usuario_cierre_id", $idUsuario, PDO::PARAM_INT);
            $stmtInsert->bindValue(":usuario_nombre", $usuarioNombre, PDO::PARAM_STR);
            $stmtInsert->bindValue(":snapshot_json", $snapshotJson, PDO::PARAM_STR);
            $stmtInsert->bindValue(":hash_integridad", $hash, PDO::PARAM_STR);
            $stmtInsert->execute();

            $stmtUpdate = $conexion->prepare("UPDATE convocatorias SET estado_en_convocatoria = 'CERRADA' WHERE id = :id");
            $stmtUpdate->bindValue(":id", $idConvocatoria, PDO::PARAM_INT);
            $stmtUpdate->execute();

            $conexion->commit();
            return "ok";
        } catch (Exception $e) {
            $conexion->rollBack();
            return "error";
        }
    }
}
