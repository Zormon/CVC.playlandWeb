<?php
$_CONFIG = parse_ini_file('config.ini', true);

if ($_CONFIG['debug']) { 
    define('TIME_START', microtime(true));
 }

const ROOT_DIR = __DIR__;
define('HOY', date('Y-m-d') );


register_shutdown_function(function() {
    $lastError = error_get_last();

    if (!empty($lastError) && $lastError['type'] == E_ERROR) {
        header('Status: 500 Internal Server Error');
        header('HTTP/1.0 500 Internal Server Error');
    }
});


$INTL = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/London', IntlDateFormatter::GREGORIAN);
// Convierte a variables post el contenido json recibido para gestión más fácil
$_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));


/* ===================================================
======================== INIT ========================
=================================================== */
require 'lib/constants.php';
require 'lib/class.db.php';
require 'lib/functions.php';
$DB = new DB($_CONFIG['driver'], $_CONFIG['server'], $_CONFIG['base'], $_CONFIG['user'], $_CONFIG['pass']);
session_start();


/* ===================================================
===================== Constantes =====================
=================================================== */
define('EVENTO_ACTUAL', \eventos\getEventoActual() );
if (EVENTO_ACTUAL) { define('P_EVENTO_ACTUAL', EVENTO_ACTUAL['evId']); }



/* ===================================================
======================= Routes =======================
=================================================== */
require "lib/router.php";
route('/api/$accion', ['api.php']);

// Web
$userInit = 'web/user/_init.php';
route('/', [$userInit, 'web/_home.php']);
route('/news', [$userInit, 'web/_news.php']);
route('/sobre-playland', [$userInit, 'web/_info.php']);
route('/eventos', [$userInit, 'web/_events.php']);
route('/eventos/$evNombre', [$userInit, 'web/_eventInfo.php']);
route('/fotos', [$userInit, 'web/_galeries.php']);
route('/fotos/$eid', [$userInit, 'web/_photos.php']);

route('/clasificaciones', [$userInit, 'web/_clasificaciones.php']);
route('/alquiler', [$userInit, 'web/_alquileres.php']);
route('/alquiler/$sNombre', [$userInit, 'web/_alquiler.php']);
route('/legal', [$userInit, 'web/_legal.php']);

// User Profile
route('/perfil', [$userInit, 'web/user/familia.php']);
route('/perfil/entrar', [$userInit, 'web/user/entrar.php']);
route('/perfil/nuevo', [$userInit, 'web/user/profileNew.php']);
route('/perfil/activar/$token', [$userInit, 'web/user/mailValidate.php']);
route('/perfil/reservas', [$userInit, 'web/user/reservas.php']);
route('/perfil/resultados', [$userInit, 'web/user/results.php']);
route('/perfil/modificar', [$userInit, 'web/user/profileModify.php']);

// Equipos
route('/perfil/equipos/nuevo', [$userInit, 'web/user/teamForm.php']);
route('/perfil/equipos/modificar/$id', [$userInit, 'web/user/teamForm.php']);

// Reservas
route('/perfil/reservas/nueva/$evShortName', [$userInit, 'web/user/reservaForm.php']);
route('/perfil/reservas/modificar/$id', [$userInit, 'web/user/reservaForm.php']);


// Monitores
route('/monitor/login', ['monitor/_login.php']);
route('/monitor', ['monitor/_inicio.php']);
route('/monitor/$prueba/$equipo', ['monitor/_prueba.php']);

// Taquilla
route('/taquilla', ['taquilla/_resumen.php']);
route('/taquilla/login', ['taquilla/_login.php']);
route('/taquilla/resumen', ['taquilla/_resumen.php']);
route('/taquilla/reservas', ['taquilla/_reservas.php']);
route('/taquilla/adultos', ['taquilla/_adultos.php']);
route('/taquilla/equipos', ['taquilla/_equipos.php']);
route('/taquilla/participaciones', ['taquilla/_participaciones.php']);


// onroad
route('/onroad/registro/meloneras', ['web/extra/onroad-registro-form.php'], ["evId"=>3]); // meloneras en DB -> 3
route('/onroad/registro/triana', ['web/extra/onroad-registro-form.php'], ["evId"=>4]); // triana en DB -> 4
route('/onroad/registro', ['web/extra/onroad-registro.php']);
route('/onroad/beacon/$evURL/$code', ['web/extra/onroad-beacon-form.php']);
route('/onroad/api/$actionString', ['web/extra/onroad-api.php']);


// Errors
route('/403', [$userInit,'views/errors/403.php']);
route('/404', [$userInit,'views/errors/404.php']);
route('/500', [$userInit,'views/errors/500.php']);


//Tests
route('/test/pass', ['.test/pass.php']);
route('/test/csv', ['.test/csv.php']);
route('/test/qr', ['.test/qr.php']);
route('/test/info', ['.test/info.php']);
route('/test/oscilo', ['.test/oscilo.html']);


header('Location:/404');