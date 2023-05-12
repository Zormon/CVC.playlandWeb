<?php
namespace eventos {
    enum timeframe {
        case past;
        case present;
        case future;
        case invalid;
    }

    function find($fields='*',$id=null,$liga=null,$nombre=null,$lugar=null,$nombreURL=null,$tipo=null) {
        global $DB;
        $q1 = $q2 = $q3 = $q4 = $q5 = '';

        if (!!$id)        { $q1 = "AND id = $id"; }
        if (!!$liga)      { $q2 = "AND liga = $liga"; }
        if (!!$nombre)    { $q3 = "AND nombre LIKE '%$nombre%'"; }
        if (!!$lugar)     { $q4 = "AND lugar LIKE '%$lugar%'"; }
        if (!!$nombreURL) { $q5 = "AND nombreURL = '$nombreURL'"; }
        if (!!$tipo)      { $q6 = "AND tipo = '$tipo'"; }

        $sql = <<<CONSULTA
            SELECT $fields FROM eventos WHERE 1 $q1 $q2 $q3 $q4 $q5 $q6
            CONSULTA;

        return $DB->consulta($sql);
    }

    function listado($ids=false,$order=null) {
        $and=''; if ($ids) { $and = 'AND eventos.id IN ('.implode(',', $ids) . ')'; }
        $orderBy=''; if ($order) { $orderBy='ORDER BY '.$order; }
        global $DB;
        $sql = <<<CONSULTA
                SELECT ev.id,ev.nombre,ev.tipo,ev.lugar,ev.fechaDesde,ev.fechaHasta,ev.nombreURL,UNIX_TIMESTAMP(ev.version) version, li.nombre nombreLiga FROM eventos ev LEFT JOIN ligas li ON ev.liga = li.id WHERE fechaHasta < CURDATE() $and $orderBy;
                SELECT ev.id,ev.nombre,ev.tipo,ev.lugar,ev.fechaDesde,ev.fechaHasta,ev.nombreURL,UNIX_TIMESTAMP(ev.version) version, li.nombre nombreLiga FROM eventos ev LEFT JOIN ligas li ON ev.liga = li.id WHERE fechaDesde <= CURDATE() AND fechaHasta >= CURDATE() $and $orderBy;
                SELECT ev.id,ev.nombre,ev.tipo,ev.lugar,ev.fechaDesde,ev.fechaHasta,ev.nombreURL,UNIX_TIMESTAMP(ev.version) version, li.nombre nombreLiga FROM eventos ev LEFT JOIN ligas li ON ev.liga = li.id WHERE fechaDesde > CURDATE() $and $orderBy;
                SELECT ev.id,ev.nombre,ev.tipo,ev.lugar,ev.fechaDesde,ev.fechaHasta,ev.nombreURL,UNIX_TIMESTAMP(ev.version) version FROM eventos ev WHERE fechaDesde > fechaHasta $and $orderBy;
                CONSULTA;
        
        $returnData['past']     = $DB->consulta($sql);
        $returnData['present']  = $DB->nextRowset();
        $returnData['future']   = $DB->nextRowset();
        $returnData['invalid']  = $DB->nextRowset();

        foreach ($returnData as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                $returnData[$k1][$k2]['entradas'] = explode(',', $v2['entradas']??'');
                $eImg = \ROOT_DIR.'/img/eventos/'.$v2['id'].'.webp';
                $returnData[$k1][$k2]['img'] = \file_exists($eImg)? $v2['id'] : 'nodef';
            }
        }

        return $returnData;
    }

    function getInfo($id) {
        global $DB, $INTL;
        $sql = <<<CONSULTA
                SELECT
                ev.id evId,
                ev.nombre evNombre,
                ev.nombreURL evNombreURL,
                ev.tipo evTipo,
                ev.lugar evLugar,
                ev.fechaDesde evDesde,
                ev.fechaHasta evHasta,
                ev.info evInfo,
                ev.data evData,
                ev.version version,
                ST_X(ev.geo) evLatitud,
                ST_Y(ev.geo) evLongitud,
                li.nombre liNombre
                FROM eventos ev
                LEFT JOIN ligas li ON ev.liga = li.id
                WHERE ev.id = $id;
                CONSULTA;
        
        $ev = $DB->consulta($sql);
        if ( @!($ev = $ev[0]) ) { return false; }

        // Imagen evento
        $eImg = \ROOT_DIR.'/img/eventos/'.$ev['evId'].'.webp';
        $ev['img'] = \file_exists($eImg)? $ev['evId'] : 'nodef';

        // Formato fechas
        $INTL->setPattern("d 'de' MMMM 'de' YYYY");
        $dt = \DateTime::createFromFormat('Y-m-d', $ev['evDesde']);
        $ev['evDesdeF'] = $INTL->format( $dt );
        $dt = \DateTime::createFromFormat('Y-m-d', $ev['evHasta']);
        $ev['evHastaF'] = $INTL->format( $dt );

        
        $ev['evData'] = json_decode( $ev['evData']??'' );
        // Entradas
        if (isset($ev['evData']->entradas)) {
            $ev['entradas'] = \entradas\listado(ids: $ev['evData']->entradas);
        } else {
            $ev['entradas'] = [];
        }

        // Pruebas
        $ev['pruebas'] = $ev['evData']->pruebas??null;


        // Días del evento
        $period = new \DatePeriod( new \DateTime($ev['evDesde']), new \DateInterval('P1D'), (new \DateTime($ev['evHasta']))->setTime(0,0,1) );
        $ev['dias'] = [];
        global $INTL;
        foreach ($period as $v) {
            $d = array();
            $INTL->setPattern("YYYY-MM-dd");
            $d['date'] = $INTL->format( $v );
            $INTL->setPattern("EEEE d");
            $d['formatted'] = $INTL->format( $v );

            array_push($ev['dias'], $d);
        }

        // Pasado, presente o futuro
        if ( $ev['evDesde'] > HOY && $ev['evHasta'] > HOY)          { $ev['timeframe'] = timeframe::future; }
        else if ( $ev['evDesde'] < HOY && $ev['evHasta'] < HOY )    { $ev['timeframe'] = timeframe::past; }
        else if ( $ev['evDesde'] <= HOY && $ev['evHasta'] >= HOY )  { $ev['timeframe'] = timeframe::present; }
        else                                                        { $ev['timeframe'] = timeframe::invalid; }

        return $ev;
    }

    function getEventoActual() {
        global $DB;
        $sql = 'SELECT id FROM eventos WHERE fechaDesde <= CURDATE() AND fechaHasta >= CURDATE()';
        $idEv = @$DB->consulta($sql)[0]['id'];
    
        if (!!!$idEv) { return false; }

        return getInfo($idEv);
    }

    function estaDisponible(int $id) {
        global $DB;
        $sql = 'SELECT id FROM eventos WHERE fechaDesde > CURDATE() AND fechaHasta > fechaDesde AND id='.$id;
        $dbData = $DB->consulta($sql);

        return count($dbData) > 0;
    }

    /**
     * Comprueba si una entrada existe y pertenece a un evento
     *
     * @param integer $entrada ID de la entrada
     * @param integer $evento ID del evento
     * @return boolean
     */
    function hasEntrada(int $entrada, int $evento) {
        global $DB;
        $sql = "SELECT id FROM eventos WHERE id=$evento AND JSON_CONTAINS(`data`, $entrada, '$.entradas')";
        $dbData = $DB->consulta($sql);

        return count($dbData) > 0;
    }

    
}
?>