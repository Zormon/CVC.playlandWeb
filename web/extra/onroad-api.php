<?php
$command = explode(',', $_['actionString']);

switch ( $command[0] ) {
    case 'beacon':
        define('EVID', $command[1]);
        define('BEACON', $command[2]);
        define('CODE', $command[3]);

        global $DB;

        $sql = 'SELECT * FROM _onroad_beacons WHERE evento='.EVID.' AND num='.BEACON;
        if ( !!!@$DB->consulta($sql)[0] ) { \system\respond(http:400); }

        $sql = 'SELECT * FROM _onroad_registros WHERE evento='.EVID.' AND id='.CODE;
        if ( !!!$reg = @$DB->consulta($sql)[0] ) { \system\respond(http:400); }

        $regData = json_decode($reg['data']);
        if ( $regData->beacon != BEACON )   { \system\respond(http:409,data:['nextBeacon'=>$regData->beacon]); }

        $regData->beacon++;
        $sql = "UPDATE _onroad_registros SET data='".json_encode($regData)."' WHERE id=".CODE;
        $DB->consulta($sql);

        \system\respond();
    break;
    
    default:
        \system\respond(http:400);
    break;
}

?>