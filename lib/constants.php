<?php

const CONST_COLORS = ['red', 'blue', 'pink', 'green', 'yellow', 'orange'];
define('CONST_COLORS_N', count(CONST_COLORS));

// Permisos de usuario
/*
const AUTH_ADMIN        = 1<<0;
const AUTH_SEE_SUPPORT  = 1<<1;
*/

// DNI
const DNI_NO_EXISTE     = 1;
const DNI_NO_ACTIVO     = 2;

// equipos
const EQUIPO_NO_PAGADO = 100;
const EQUIPO_SIN_INTENTOS = 101;



function print_javascript_constants() {
    $consts = get_defined_constants(true)['user'];
    $str = '';
    foreach ($consts as $con => $val) {
        $type = gettype($val);
        $admit = ['boolean', 'integer', 'string', 'double'];
        if ( 
            substr($con, 0, 2) == 'P_'
            && array_search($type, $admit) !== true  
        ) { $str .= "const $con=$val;"; }
    }
    return $str;
}

?>