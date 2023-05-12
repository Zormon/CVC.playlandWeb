<?php
$command = explode(',', $_['accion']);
switch( $command[0] ) {
    case 'adulto':
        require(ROOT_DIR.'/lib/api/api.adultos.php');
    break;  
    case 'equipo':
        require(ROOT_DIR.'/lib/api/api.equipos.php');
        break;    
        
    case 'reserva':
        require(ROOT_DIR.'/lib/api/api.reserva.php');
        break;
        
    case 'monitor':
        require(ROOT_DIR.'/lib/api/api.monitor.php');
    break;
        
    case 'admin':
        require(ROOT_DIR.'/lib/api/api.admin.php');
        break;
        
    case 'taquilla':
        require(ROOT_DIR.'/lib/api/api.taquilla.php');
    break;
}

?>