<?php
namespace resumen {
    function plataforma() {
        global $DB;

        $sql = <<<CONSULTA
                SELECT
                    (SELECT COUNT(*) FROM adultos WHERE estado='activo') AS numAdultosActivos,
                    (SELECT COUNT(*) FROM equipos INNER JOIN adultos ON equipos.adulto = adultos.id WHERE adultos.estado='activo') AS numEquiposActivos,
                CONSULTA;

        $dbData = $DB->consulta($sql)[0];
        return $dbData;
    }

    function evento($id) {
        global $DB;
        $sql = <<<CONSULTA
                SELECT
                    (SELECT COUNT(*) FROM reservas WHERE pagado!='no' AND evento=$id) AS numReservas,
                    (SELECT COUNT(*) FROM participaciones WHERE evento=$id) AS numParticipaciones
                CONSULTA;

        $dbData = $DB->consulta($sql)[0];
        return $dbData;
    }

    

    function rankings($evId) {
        $equipos = \equipos\listado();
        $resultados = \participaciones\resultados($evId);

        foreach ($equipos as $key => $eq) { $equipos[$key]['puntos'] = 0;}

        foreach ($resultados as $res) {
            switch ($res['puesto']) {
                case '1': $equipos[$res['equipo']]['puntos'] += 10; break;
                case '2': $equipos[$res['equipo']]['puntos'] += 9; break;
                case '3': $equipos[$res['equipo']]['puntos'] += 8; break;
                case '4': $equipos[$res['equipo']]['puntos'] += 7; break;
                case '5': $equipos[$res['equipo']]['puntos'] += 6; break;
                case '6': $equipos[$res['equipo']]['puntos'] += 5; break;
                case '7': $equipos[$res['equipo']]['puntos'] += 4; break;
                case '8': $equipos[$res['equipo']]['puntos'] += 3; break;
                default:  $equipos[$res['equipo']]['puntos'] += 1; break;
            }
        }

        $ranking = array();
        foreach ($equipos as $eq) {
            if ($eq['puntos'] > 0) {
                array_push( $ranking, $eq );
            }
        }

        usort($ranking, function($a,$b) { return $a['puntos'] < $b['puntos']; });
        $returnData['ranking'] = $ranking;

        return $returnData;
    }
}
?>