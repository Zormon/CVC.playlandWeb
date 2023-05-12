<?php

require(ROOT_DIR.'/lib/class.monitor.php');
$MONITOR = new monitor();
switch ($command[1]) {
    case 'checkQr':
        require(ROOT_DIR.'/lib/class.equipo.php');
        $equipo = new equipo($_POST['eq']);
        if ( !$equipo->checkQr($_POST['qr']) ) { \system\respond(0x0424); } // qr invalido
        \system\respond(0x0000);
    break;

    case 'login':
        if ( $MONITOR->isLogged )    { \system\respond(0x0400); }
        if ( @!!!$_POST['pass'] )    { \system\respond(0x0402); }

        if ($MONITOR->login( $_POST['pass'] ))   { \system\respond(0x0000); }
        else                                     { \system\respond(0x0403); }
    break;
    case 'logout':
        if ($MONITOR->unlog()) { \system\respond(0x0000); };
        break;
    
    case 'participacion':
        require(ROOT_DIR.'/lib/class.equipo.php');
        /* No estoy comprobando validez de los datos, se supone que
         aqui los datos que llegan de las tablet deberian ser correctos */

        $equipo = new equipo($_POST['equipo']);

        @list($pruebaReq, $obstaculoReq) = explode(',', $_POST['prueba']);
        $prueba = \pruebas\find(id:$pruebaReq)[0];

        if ($equipo->intentosRestantes($prueba['id']) < 1) { \system\respond(0x0411); } // Sin intentos
        
        $EA = EVENTO_ACTUAL;
        if ( $prueba['tipo'] == 'race' ) {
            switch ($obstaculoReq) {
                case 's': // Salida
                    $a = \participaciones\find(evento:$EA['evId'], equipo:$equipo->id, prueba:$prueba['id'], resultado:-1);
                    if ($a) { \system\respond(0x0420); } // La race está activa

                    $data = json_encode( array("superado"=>[],"fallado"=>[]) );

                    $sql =  <<<CONSULTA
                            INSERT INTO participaciones(
                                evento,
                                equipo,
                                fecha,
                                monitor,
                                prueba,
                                resultado,
                                data)
                            VALUES(
                                {$EA['evId']},
                                {$equipo->id},
                                NOW(),
                                {$MONITOR->id},
                                {$prueba['id']},
                                -1,
                                '$data')
                            CONSULTA;
                break;
                case 'e': // Meta
                    $p = @\participaciones\find(ts:true, evento:$EA['evId'], equipo:$equipo->id, prueba:$prueba['id'], resultado:-1)[0];
                    if (!$p) { \system\respond(0x0421); } // No ha empezado la race
                    $resultado = time() - (int)$p['ts'];

                    $sql =  <<<CONSULTA
                            UPDATE participaciones SET
                            resultado = $resultado
                            WHERE id = {$p['id']}
                            CONSULTA;
                break;
                
                default: /// Obstaculo
                    if (!is_numeric($obstaculoReq) )                                            { \system\respond(0x0422); } // obstaculo invalido
                    $p = @\participaciones\find(evento:$EA['evId'], equipo:$equipo->id, prueba:$prueba['id'], resultado:-1)[0];
                    if (!$p)                                                                    { \system\respond(0x0421); } // No ha empezado la race
                    if ( array_search((int)$obstaculoReq, $p['data']->superado)!== false
                        || array_search((int)$obstaculoReq, $p['data']->fallado)!== false )    { \system\respond(0x0423); } // ya lo ha pasado

                    if ($_POST['resultado'] == 'true') {
                        array_push( $p['data']->superado, (int)$obstaculoReq );
                    } else {
                        array_push( $p['data']->fallado, (int)$obstaculoReq );
                    }
                    $data = json_encode($p['data']);

                    $sql =  <<<CONSULTA
                            UPDATE participaciones SET
                            data = '$data'
                            WHERE id = {$p['id']}
                            CONSULTA;
                break;
            }
        } else {
            $sql =  <<<CONSULTA
                    INSERT INTO participaciones(evento,equipo,fecha,prueba,resultado)
                    VALUES({$EA['evId']},{$equipo->id},NOW(), {$prueba['id']}, {$_POST['resultado']})
                    CONSULTA;
        }

        global $DB;
        $DB->consulta($sql);
        \system\respond(0x0000);
    break;
}

?>