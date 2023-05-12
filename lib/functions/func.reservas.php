<?php
namespace reservas {
    enum pago {
        case no;
        case taquilla;
        case online;
    }

    function find( int $id=null, int $equipo=null, int $evento=null, pago $pagado=null, \DateTime $dia=null ) {
        global $DB; global $INTL;
        $q1 = $q2 = $q3 = $q4 = $q5 = '';

        if ($id)        { $q1 = "AND id = $id"; }
        if ($equipo)    { $q2 = "AND equipo = $equipo"; }
        if ($evento)    { $q3 = "AND evento = $evento"; }
        if ($pagado)    { $q4 = "AND pagado = '$pagado->value'"; }
        if ($dia)       { 
            $INTL->setPattern("YYYY-MM-dd");
            $q4 = "AND dia = '".$INTL->format($dia)."'";
        }

        $sql = <<<CONSULTA
            SELECT * FROM reservas WHERE 1 $q1 $q2 $q3 $q4 $q5
            CONSULTA;

        return $DB->consulta($sql);
    }

    function listado($ids=false, $table=false, $evento=false) {
        global $DB;

        $whereEv = '';
        if ($evento) { $whereEv = 'WHERE evento=' . $evento; }
        
        if ($table) {
            $EV = EVENTO_ACTUAL;
            $sql = <<<CONSULTA
                    SELECT 
                    reservas.id id,
                    equipos.titulo equipo,
                    adultos.nombre adulto,
                    reservas.dia dia,
                    DATE_FORMAT(reservas.fecha, '%d-%m-%Y %H:%i') fecha,
                    entradas.nombre entrada,
                    reservas.pagado pagado,
                    equipos.id equipoId,
                    adultos.id adultoId,
                    entradas.id entradaId
                    FROM reservas
                    INNER JOIN equipos ON reservas.equipo = equipos.id
                    INNER JOIN adultos ON equipos.adulto = adultos.id
                    INNER JOIN entradas ON reservas.entrada = entradas.id
                    $whereEv
                    ORDER BY fecha DESC, id DESC
                    CONSULTA;
        }
        else {
            $sql = 'SELECT * FROM reservas';
            if ($ids) { $sql .= ' WHERE id IN ('.implode(',', $ids) . ')'; }
        }

        $dbData = $DB->consulta($sql);
        $returnData = array();
        global $INTL;
        foreach ($dbData as $dbRow) { 
            $INTL->setPattern("EEEE d");
            $dt = \DateTime::createFromFormat('Y-m-d', $dbRow['dia']);
            $dbRow['diaF'] = $INTL->format( $dt );
            $returnData[$dbRow['id']] = $dbRow; 
        }
        return $returnData;
    }

    function edit($data) {
        global $DB;

        $sql = <<<CONSULTA
            UPDATE reservas SET 
            equipo = {$data['equipo']},
            entrada = {$data['entrada']},
            dia = '{$data['dia']}',
            pagado = '{$data['pagado']}'
            WHERE id={$data['id']}
            CONSULTA;

        $DB->consulta($sql, false);
        return true;
    }

    function add($equipo, $entrada, $evento, \Datetime $dia, pago $pago=pago::no) {
        global $DB, $INTL;

        $INTL->setPattern("YYYY-MM-dd");
        $dia = $INTL->format($dia);

        $sql = <<<CONSULTA
            INSERT INTO reservas(equipo, entrada, evento, dia, pagado)
            VALUES($equipo,$entrada,$evento,'$dia','$pago->name')
            CONSULTA;
            

        try {
            $t = $DB->consulta($sql, false);
            return 0;
        } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }
    }

    function delete($id) {
        global $DB;


        $sql = 'DELETE FROM reservas WHERE id='.$id;
        $DB->consulta($sql, false);
        return true;
    }

    //TODO: Usar las funciones de añadir adultos y equipos para esto
    function quickBuy($data) {
        global $DB;
        $EV = P_EVENTO_ACTUAL;

        $sql = <<<CONSULTA
                INSERT INTO adultos(DNI, nombre, email, telefono, estado)
                VALUES('{$data['DNI']}', '{$data['nombre']}', '{$data['email']}', '{$data['telefono']}', 'activo');
                CONSULTA;

        try {
            $DB->consulta($sql, false);
        } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }

        $adultoId = $DB->lastInsertId();

        // equipo 1
        $qrToken = bin2hex(random_bytes(2));
        $sql = <<<CONSULTA
                INSERT INTO equipos(titulo, nombre, nacimiento, bando, adulto, qrToken)
                VALUES('{$data['equipo1']}', '{$data['participante1']}', '{$data['nacimiento1']}', '{$data['bando1']}', $adultoId, '$qrToken')
                CONSULTA;
        try {
            $DB->consulta($sql, false);
        } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }

        $eqId1 = $DB->lastInsertId();

        // compra 1
        $sql = <<<CONSULTA
                INSERT INTO reservas(equipo, entrada, evento, dia, pagado)
                VALUES($eqId1, {$data['entrada1']}, $EV, '{$data['dia']}', 'taquilla')
                CONSULTA;
        try {
            $DB->consulta($sql, false);
        } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }

        if (@$data['equipo2'] != '') {
            // equipo 2
            $qrToken = bin2hex(random_bytes(2));
            $sql = <<<CONSULTA
                    INSERT INTO equipos(titulo, nombre, nacimiento, bando, adulto, qrToken)
                    VALUES('{$data['equipo2']}', '{$data['participante2']}', '{$data['nacimiento2']}', '{$data['bando2']}', $adultoId, '$qrToken')
                    CONSULTA;
            try {
                $DB->consulta($sql, false);
            } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }

            $eqId2 = $DB->lastInsertId();

            // compra 2
            $sql = <<<CONSULTA
                    INSERT INTO reservas(equipo, entrada, evento, dia, pagado)
                    VALUES($eqId2, {$data['entrada2']}, $EV, '{$data['dia']}', 'taquilla')
                    CONSULTA;
            try {
                $DB->consulta($sql, false);
            } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }
        }

        if (@$data['equipo3'] != '') {
            // equipo 3
            $qrToken = bin2hex(random_bytes(2));
            $sql = <<<CONSULTA
                    INSERT INTO equipos(titulo, nombre, nacimiento, bando, adulto, qrToken)
                    VALUES('{$data['equipo3']}', '{$data['participante3']}', '{$data['nacimiento3']}', '{$data['bando3']}', $adultoId, '$qrToken')
                    CONSULTA;
            try {
                $DB->consulta($sql, false);
            } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }

            $eqId3 = $DB->lastInsertId();

            // compra 3
            $sql = <<<CONSULTA
                    INSERT INTO reservas(equipo, entrada, evento, dia, pagado)
                    VALUES($eqId3, {$data['entrada3']}, $EV, '{$data['dia']}', 'taquilla')
                    CONSULTA;
            try {
                $DB->consulta($sql, false);
            } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }
        }

        if (@$data['equipo4'] != '') {
            // equipo 4
            $qrToken = bin2hex(random_bytes(2));
            $sql = <<<CONSULTA
                    INSERT INTO equipos(titulo, nombre, nacimiento, bando, adulto, qrToken)
                    VALUES('{$data['equipo4']}', '{$data['participante4']}', '{$data['nacimiento4']}', '{$data['bando4']}', $adultoId, '$qrToken')
                    CONSULTA;
            try {
                $DB->consulta($sql, false);
            } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }

            $eqId4 = $DB->lastInsertId();

            // compra 4
            $sql = <<<CONSULTA
                    INSERT INTO reservas(equipo, entrada, evento, dia, pagado)
                    VALUES($eqId4, {$data['entrada4']}, $EV, '{$data['dia']}', 'taquilla')
                    CONSULTA;
            try {
                $DB->consulta($sql, false);
            } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }
        }

        return 0;
        
    }

    
    function equiposDisponibles(int $evento, array $ids, \DateTime $fecha) {
        global $DB;
        global $INTL;
        $eqIds = implode(',', $ids );
        $INTL->setPattern("YYYY-MM-dd");
        $qd = $INTL->format($fecha);

        $sql = <<<CONSULTA
                select equipo FROM reservas WHERE evento=$evento AND equipo IN ($eqIds) AND dia='$qd'
                CONSULTA;

        $discardEqs = @$DB->consulta($sql)[0];
        if (!!!$discardEqs) { return $ids; }

        $equipos = array();
        foreach ($ids as $e) {
            if ( !array_search($e, $discardEqs) ) {
                array_push($equipos, $e);
            }
        }

        return $equipos;
    }
}
?>