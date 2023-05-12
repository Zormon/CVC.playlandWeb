<?php
namespace participaciones {
    function find( 
            bool $ts=false,
            int $id=null,
            int $evento=null,
            int $equipo=null,
            \datetime $fecha=null,
            int $monitor=null,
            int $prueba=null,
            int $resultado=null
        ) {
        global $DB;
        global $INTL;
        $qts = $q1 = $q2 = $q3 = $q4 = $q5 = $q6 = $q7 ='';

        if ($ts) { $qts = ', UNIX_TIMESTAMP(FECHA) ts'; }

        if ($id)        { $q1 = "AND id = $id"; }
        if ($evento)    { $q2 = "AND evento = $evento"; }
        if ($equipo)    { $q3 = "AND equipo = $equipo"; }
        if ($fecha)     {
            $INTL->setPattern("YYYY-MM-dd HH:mm:ss");
            $q4 = "AND fecha = '".$INTL->format($fecha)."'";
        }
        if ($monitor)   { $q5 = "AND monitor = $monitor"; }
        if ($prueba)    { $q6 = "AND prueba = $prueba"; }
        if ($resultado) { $q7 = "AND resultado = $resultado"; }

        $sql = <<<CONSULTA
            SELECT *$qts FROM participaciones WHERE 1 $q1 $q2 $q3 $q4 $q5 $q6 $q7
            CONSULTA;

        $dbData = $DB->consulta($sql);
        foreach ($dbData as $k => $v) {
            $dbData[$k]['data'] = \json_decode( $dbData[$k]['data'] );
        }

        return $dbData;
    }

    function listado($ids=false, $event=false) {
        global $DB;

        $where = '';
        if ($event) { $where = 'WHERE evento='.$event; }

        $sql = <<<CONSULTA
        SELECT 
        participaciones.id id,
        participaciones.resultado resultado,
        DATE_FORMAT(participaciones.fecha, '%d-%m-%Y %H:%i') fecha,
        participaciones.data data,
        equipos.titulo equipo,
        equipos.id equipoId,
        pruebas.nombre prueba,
        pruebas.id pruebaId,
        pruebas.tipo pruebaTipo
        FROM participaciones
        INNER JOIN equipos ON participaciones.equipo = equipos.id
        INNER JOIN pruebas ON participaciones.prueba = pruebas.id
        $where
        ORDER BY fecha DESC
        CONSULTA;
        
        if ($ids) { $sql .= ' WHERE participaciones.id IN ('.implode(',', $ids) . ')'; }
        
        $dbData = $DB->consulta($sql);
        $returnData = array();
        foreach ($dbData as $dbRow) { 
            $dbRow['data'] = \json_decode( $dbRow['data'] );
            $returnData[$dbRow['id']] = $dbRow; 
        }
        return $returnData;
    }

    function edit($data) {
        global $DB;

        $EV = EVENTO_ACTUAL;
        $sql = <<<CONSULTA
        UPDATE participaciones SET 
        evento = $EV,
        equipo = {$data['equipo']},
        monitor = {$data['monitor']},
        prueba = {$data['prueba']},
        resultado = {$data['resultado']}
        WHERE id={$data['id']}
        CONSULTA;

        $DB->consulta($sql, false);
        return true;
    }

    function add($data) {
        global $DB;

        $EV = EVENTO_ACTUAL;
        $sql = <<<CONSULTA
                INSERT INTO participaciones(evento, equipo, monitor, prueba, resultado)
                VALUES(
                    $EV,
                    {$data['equipo']},
                    {$data['monitor']},
                    {$data['prueba']},
                    {$data['resultado']}
                    )
                CONSULTA;

        try {
            $DB->consulta($sql, false);
            return 0;
        } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }
    }

    function delete($id) {
        global $DB;

        $sql = <<<CONSULTA
                DELETE FROM participaciones WHERE id=$id;
                CONSULTA;

        $DB->consulta($sql, false);
        return true;
    }

    function resultados($evId) {
        global $DB;
        $sql = <<<CONSULTA
                DROP TABLE IF EXISTS resultados;
                CREATE TEMPORARY TABLE resultados(prueba INT, equipo INT, tipo VARCHAR(15), resultado INT) ENGINE=MEMORY;
                INSERT INTO resultados SELECT prueba,equipo,pruebas.tipo, IF(tipo='Velocidad',MIN(resultado),MAX(resultado)) AS resultado FROM participaciones INNER JOIN pruebas ON pruebas.id=participaciones.prueba WHERE evento=$evId GROUP BY equipo,prueba;
                
                SELECT *, ROW_NUMBER() OVER (PARTITION BY prueba ORDER BY 1, 
                    case when tipo='Velocidad' then resultado END ASC,
                    case when tipo='Puntos' OR tipo='Aguante' then resultado END DESC) AS puesto
                FROM resultados ORDER BY prueba,
                    CASE WHEN tipo='Puntos' OR tipo='Aguante' THEN resultado END DESC,
                    CASE WHEN tipo='Velocidad' THEN resultado END ASC;
                CONSULTA;

        $DB->consulta($sql);
        $DB->nextRowset();
        $DB->nextRowset();
        $dbData = $DB->nextRowset();

        return $dbData;
    }
}
?>