<?php
require(ROOT_DIR.'/lib/class.user.php');
require(ROOT_DIR.'/lib/class.equipo.php');
$USER = new user();
switch ($command[1]) {
    case 'nueva':
        if (!$USER->isLogged) { \system\respond(0x0001); }
        // ====== Validacion de datos
        if ( @!!!$_POST['equipos'][0] )                                     { \system\respond(0x0300); } // No hay equipos
        $_POST['equipos'] = (array)$_POST['equipos'];
        foreach ($_POST['equipos'] as $e) { if (!is_numeric($e))            { \system\respond(0x0301); } } // Algun equipo no es numerico
        if ( @!!!$_POST['entrada'] || !is_numeric($_POST['entrada']))       { \system\respond(0x0302); } // No hay entrada o no es numerica
        if ( !\eventos\hasEntrada($_POST['entrada'], $_POST['evento']))     { \system\respond(0x0303); } // Ese numero de entrada no existe en el evento
        if ( @!!!$_POST['evento'] )                                         { \system\respond(0x0304); } // Evento no especificado
        if ( !is_numeric($_POST['evento']) )                                { \system\respond(0x0305); } // Evento no numerico
        if ( !\eventos\estaDisponible($_POST['evento']) )                   { \system\respond(0x0306); } // Evento no disponible
        
        $dia = DateTime::createFromFormat('Y-m-d', $_POST['dia']);

        $eqsDisponibles = \reservas\equiposDisponibles($_POST['evento'], array_column($USER->equipos, 'id'), $dia);
        $diff = array_diff( $_POST['equipos'], $eqsDisponibles );
        if (count($diff) > 0) { \system\respond(0x0307); } //Algun equipo no está disponible para este evento y dia
        // ====== / Validacion de datos
        
        foreach ($_POST['equipos'] as $eq) { \reservas\add($eq, $_POST['entrada'], $_POST['evento'], $dia); } 
        \system\respond(0x0000);
    break;
    case 'cancelar':
        if (!$USER->isLogged) { \system\respond(0x0001); }
        // ====== Validacion de datos
        if ( @!!!$command[2])                                       { \system\respond(0x0308); } 
        if ( !is_numeric($command[2]) )                             { \system\respond(0x0309); } 
        $reserva = \reservas\listado( [$command[2]]);
        if ( count($reserva) == 0 )                                 { \system\respond(0x030A); } // Reserva no existe
        if ( !$USER->getEquipo( $reserva[$command[2]]['equipo'] ))  { \system\respond(0x0001); } // La reserva no es del usuario
        if ( $reserva[$command[2]]['pagado'] != 'no')               { \system\respond(0x030B); } // La reserva ya esta pagada
        // ====== / Validacion de datos

        \reservas\delete( $command[2] );
        \system\respond(0x0000);
    break;
}

        ?>