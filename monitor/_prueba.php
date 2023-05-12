<?php
    function alertError(string $msg, string $url):void {
        $html = <<<TEXT
            <!DOCTYPE html><html><body>
                <script>alert('❌ $msg');location='$url'</script>
            </body></html>
            TEXT;

        echo $html;
        die;
    }

    require(__DIR__.'/../lib/class.monitor.php');
    require(__DIR__.'/../lib/class.equipo.php');
    
    $MONITOR = new monitor();
    if (!$MONITOR->isLogged) { header('Location: /monitor/login'); exit(); }
    if ( !isset($_['equipo']) || $_['equipo'] == '') { header('Location: /monitor'); die; }
    
    /* =========== Comprobaciones ============= */
    $redirect = '/monitor';
    $equipo = new equipo( $_['equipo'] );
    
    $reqPrueba = explode(',', $_['prueba']);
    $prueba = \pruebas\find(id:$reqPrueba[0])[0];

    if (EVENTO_ACTUAL['evData']==NULL)                     { alertError('El evento actual no tiene información suficiente', $redirect); }
    
    if (!$equipo->id)                                      { alertError('No existe el equipo', $redirect); }
    if (!$equipo->haPagado())                              { alertError('El equipo no ha pagado', $redirect); }
    
    if (!$prueba)                                                                           { alertError('No existe la prueba', $redirect); }
    if ( array_search($prueba['id'], EVENTO_ACTUAL['pruebas'])===false )                    { alertError('Este evento no dispone de esta prueba', $redirect); }
    if ($equipo->intentosRestantes($prueba['id']) < 1)                                      { alertError('El equipo no tiene intentos disponibles', $redirect); }
    
    // --- Race ---
    if ($prueba['tipo'] == 'race') {
        if ( @!!!$reqPrueba[1] )                                                            { alertError('Obstaculo no especificado', $redirect); }

        switch ($reqPrueba[1]) {
            case 's': // Salida
                $a = @\participaciones\find(evento:P_EVENTO_ACTUAL, equipo:$equipo->id, prueba:$prueba['id'], resultado:-1)[0];
                if ($a) { alertError('El equipo ya ha empezado la race', $redirect); }
            break;
            case 'e': // Meta
                $p = @\participaciones\find(evento:P_EVENTO_ACTUAL, equipo:$equipo->id, prueba:$prueba['id'], resultado:-1)[0];
                if (!$p) { alertError('El equipo no ha empezado la race', $redirect); }
            break;
            default: // Obstaculo
                if (!is_numeric($reqPrueba[1]) )                                            { alertError('Obstaculo inválido', $redirect); }
                $obstaculo = @\obstaculos\find(id:$reqPrueba[1])[0];
                if ( !$obstaculo )                                                          { alertError('No existe el obstaculo', $redirect); }
                // La prueba dispone del obstaculo ?
                if ( array_search($obstaculo['id'], $prueba['data']->obstaculos)===false )  { alertError('La prueba no dispone del obstáculo', $redirect); }   

                $p = @\participaciones\find(evento:P_EVENTO_ACTUAL, equipo:$equipo->id, prueba:$prueba['id'], resultado:-1)[0];
                if (!$p) { alertError('El equipo no ha empezado la race', $redirect); }
                if ( array_search($obstaculo['id'], $p['data']->superado)!==false )         { alertError('El equipo ya ha superado el obstaculo', $redirect); }
                if ( array_search($obstaculo['id'], $p['data']->fallado)!==false )          { alertError('El equipo ya ha fallado el obstaculo', $redirect); }
            break;
        }
    }
    // --- / Race ---

    /* =========== / Comprobaciones ============= */
?>

<?php include('header.php');?>

<body class="app equipos">
    <header>
        <button id="backStart"><i class="icon-left"></i></button>
        <p><?=$prueba['nombre']?><?=@$obstaculo?', '.$obstaculo['nombre']:''?></p>
        <button id="logout"><i class="icon-salir"></i></button>
    </header>
    <hr class="colorLines">
    <main>
        <h1><?=$equipo->titulo?></h1>
        <?php
            switch($prueba['tipo']) {
                case 'puntos':
                    include(__DIR__.'/pruebas/puntos.php');
                break;
                case 'aguante': case 'velocidad':
                    include(__DIR__.'/pruebas/tiempo.php');
                    break;
                case 'race':
                    switch ($reqPrueba[1]) {
                        case 's': include(__DIR__.'/pruebas/salida.php'); break;
                        case 'e': include(__DIR__.'/pruebas/meta.php'); break;
                        default: include(__DIR__.'/pruebas/obstaculo.php'); break;
                    }
                break;
            }
        ?>
    </main>
    <?php include('footer.php');?>
    <script type="module">
        import {PRUEBA} from '/js/module-monitor.min.js?v4'
        new PRUEBA('<?=$_['prueba']?>', '<?=(int)$_['equipo']?>')
    </script>
</body>
</html>