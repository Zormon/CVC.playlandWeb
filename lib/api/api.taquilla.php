<?php

require(ROOT_DIR.'/lib/class.admin.php');
$ADMIN = new admin();
if ( !$ADMIN->isLogged )                                            { \system\respond(0x0001); }

switch ( $command[1] ) {
    case 'resumen':
        switch ($command[2]) {
            case 'general':
                $data['plataforma'] = \resumen\plataforma();
                if (EVENTO_ACTUAL) {
                    $data['evento'] = \resumen\evento(P_EVENTO_ACTUAL);
                    $data['rankings'] = \resumen\rankings(P_EVENTO_ACTUAL);
                }
                \system\respond(data:$data);
            break;
        }
    break;
    case 'reservas':
        switch ( $command[2] ) {
            case 'lista':
                if (EVENTO_ACTUAL) {
                    $response = \reservas\listado(table:true, evento:P_EVENTO_ACTUAL);
                    if ( count($response) > 0 )     { \system\respond(0x0000, $response);}
                    else                            { \system\respond(http:204); }
                } else { \system\respond(status:0x0620,http:404); }
            break;

            case 'add':
                if (EVENTO_ACTUAL) {
                    $dia = Datetime::createFromFormat('Y-m-d', $_POST['dia']);
                    $code = \reservas\add($_POST['equipo'], $_POST['entrada'], P_EVENTO_ACTUAL, $dia, \reservas\pago::taquilla);
                    switch ( $code ) {
                        case 0:     \system\respond(0x0000); break;
                        case 1062:  \system\respond(0x0600); break; // equipo ya ha pagado
                        default:    \system\respond(0x06FE, 'codigo: '.$code); break;
                    }
                } else { \system\respond(http:500); }
            break;

            case 'edit':
                \reservas\edit($_POST);
                \system\respond(0x0000);
            break;

            case 'quickBuy':
                if ( \adultos\find(DNI:$_POST['DNI']) )              { \system\respond(0x0601); } // Adulto ya existe
                if ( \equipos\find(titulo:$_POST['equipo1']) )       { \system\respond(0x0602); } // equipo 1 ya existe
                if ( @!!$_POST['equipo2'] ) {
                    if ( \equipos\find(titulo:$_POST['equipo2']) )   { \system\respond(0x0603); } // equipo 2 ya existe
                }
                if ( @!!$_POST['equipo3'] ) {
                    if ( \equipos\find(titulo:$_POST['equipo3']) )   { \system\respond(0x0604); } // equipo 3 ya existe
                }
                if ( @!!$_POST['equipo4'] ) {
                    if ( \equipos\find(titulo:$_POST['equipo4']) )   { \system\respond(0x0604); } // equipo 4 ya existe
                }

                \reservas\quickBuy($_POST);
                \system\respond(0x0000);
            break;
                
            case 'delete':
                \reservas\delete($_POST['id']);
                \system\respond(0x0000);
            break;
        }
    break;
    case 'adultos':
        switch ( $command[2] ) {
            case 'lista':
                \system\respond(0x0000, \adultos\listado(false) );
            break;
            case 'add':
                if ( \adultos\find(DNI:$_POST['DNI']) )             { \system\respond(0x0114); }
                if ( \adultos\find(email:$_POST['email']) )         { \system\respond(0x0115); }
                if ( \adultos\find(telefono:$_POST['telefono']) )   { \system\respond(0x0116); }

                \adultos\add($_POST);
                \system\respond(0x0000);
            break;
            case 'edit':
                $cur = \adultos\find(id:$_POST['id'])[0];

                if ( $cur['DNI'] != $_POST['DNI'] && \adultos\find(DNI:$_POST['DNI']) )                     { \system\respond(0x0114); }
                if ( $cur['email'] != $_POST['email'] && \adultos\find(email:$_POST['email']) )             { \system\respond(0x0115); }
                if ( $cur['telefono'] != $_POST['telefono'] && \adultos\find(telefono:$_POST['telefono']) ) { \system\respond(0x0116); }
                
                \adultos\edit($_POST);
                \system\respond(0x0000);
            break;
            case 'delete':
                \adultos\delete($_POST['id']);
                \system\respond(0x0000);
            break;
        }
    break;
    case 'equipos':
        switch ( $command[2] ) {
            case 'lista':
                \system\respond(0x0000, \equipos\listado(false) );
            break;
            case 'add':
                if( \equipos\find(titulo:$_POST['titulo']))     { \system\respond(0x0610); }

                \equipos\add($_POST, $_POST['adulto']);
                \system\respond(0x0000);
            break;
            case 'edit':
                $cur = \equipos\find(id:$_POST['id'])[0];
                if ( $cur['titulo'] != $_POST['titulo'] && \equipos\find(titulo:$_POST['titulo']) )        { \system\respond(0x0610); }

                \equipos\edit($_POST);
                \system\respond(0x0000);
            break;
                
            case 'delete':
                \equipos\delete($_POST['id']);
                \system\respond(0x0000);
            break;
        }
    break;
    case 'participaciones':
        switch ( $command[2] ) {
            case 'lista':
                if (EVENTO_ACTUAL) {
                    $response = \participaciones\listado(event:P_EVENTO_ACTUAL);
                    if ( count($response) > 0 )     { \system\respond(0x0000, $response );}
                    else                            { \system\respond(http:204); }
                } else { \system\respond(status:0x0620,http:404); }
            break;
            case 'edit':
                if ( isset($_POST['id']) ) {
                    \participaciones\edit($_POST);
                    \system\respond(0x0000);
                } else {
                    $code = \participaciones\add($_POST);
                    switch ( $code ) {
                        case 0: \system\respond(0x0000); break;
                        default: \system\respond(0x06FE, 'codigo: '.$code); break;
                    }
                }
            break;
                
            case 'delete':
                \participaciones\delete($_POST['id']);
                \system\respond(0x0000);
                break;
        }
    break;
}

?>