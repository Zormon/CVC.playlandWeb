<?php

require(ROOT_DIR.'/lib/class.equipo.php');
require(ROOT_DIR.'/lib/class.user.php');
$USER = new user();
if (!$USER->isLogged) { \system\respond(0x0001); }

switch($command[1]) {
    case 'nuevo': case 'modificar':
        define('ADD', $command[1]=='nuevo');
        if ( !ADD ) { 
            if ( @!!!$_POST['id'] )                                 { \system\respond(0x0200); }
            if ( !$USER->getEquipo($_POST['id']) )                  { \system\respond(0x0001); }
        }

        // Titulo
        if (@!!!$_POST['titulo'])                                   { \system\respond(0x0201); }
        if (strlen($_POST['titulo']) < 3)                           { \system\respond(0x0202); }
        if (strlen($_POST['titulo']) > 80)                          { \system\respond(0x0203); }
        if (preg_match('/[^0-9a-zA-ZÑñ ]/', $_POST['titulo']))      { \system\respond(0x0204); }
        
        // Nombre
        if (@!!!$_POST['nombre'])                                   { \system\respond(0x0205); }
        if (strlen($_POST['nombre']) < 3)                           { \system\respond(0x0206); }
        if (strlen($_POST['nombre']) > 80)                          { \system\respond(0x0207); }
        if (preg_match('/[^a-zA-ZÑñ ]/', $_POST['nombre']))         { \system\respond(0x0208); }
        
        // Nacimiento
        if (@!!!$_POST['nacimiento'])                               { \system\respond(0x0209); }
        if (!strtotime($_POST['nacimiento']))                       { \system\respond(0x020A); }
        

        if ($command[1] == 'nuevo') {
            if ( \equipos\find(titulo:$_POST['titulo']) )           { \system\respond(0x020B); }
        } else {
            $eq = \equipos\find(id:$_POST['id'])[0];
            if ( $eq['titulo'] != $_POST['titulo'] ) { 
                if (\equipos\find(titulo:$_POST['titulo']))         { \system\respond(0x020B); }
            }
        }
        
        if (ADD)    { \equipos\add($_POST, $USER->id); }
        else        { \equipos\edit($_POST); }

        \system\respond(0x0000);
        break;
    case 'borrar':
        if ( @!!!$command[2] )                      { \system\respond(0x020B, 'Datos insuficientes'); }
        if ( !$USER->getEquipo($command[2]) )       { \system\respond(0x020C, 'Sin permisos'); }

        \equipos\delete($command[2]);
        \system\respond(0x0000);
        break;
}

?>