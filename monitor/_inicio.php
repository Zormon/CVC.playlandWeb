<?php
    require(__DIR__.'/../lib/class.monitor.php');

    
    // ======================== LOGIN PROCESS ====================
    $MONITOR = new monitor();
    if (!$MONITOR->isLogged) { header('Location: /monitor/login'); exit(); }

    include('header.php');
?>

<body class="app start">
    <header>
        <select id="prueba">
            <?php
            $pruebas = \pruebas\listado( EVENTO_ACTUAL['pruebas'] );
            foreach ($pruebas as $p) {
                switch ($p['tipo']) {
                    case 'puntos': $icon = '💯 '; break;
                    case 'velocidad': $icon = '⌚ '; break;
                    case 'aguante': $icon = '⏳ '; break;
                    default: $icon = ''; break;
                }
                if ($p['tipo'] == 'race') { ?>
                <option value="<?=$p['id']?>,s">🏃 <?=$p['nombre']?>, Salida
                <option value="<?=$p['id']?>,e">🏁 <?=$p['nombre']?>, Meta
                <?php
                $obIDs = $p['data']->obstaculos;
                $obstaculos = \obstaculos\listado($obIDs);

                foreach ($obstaculos as $o) { ?>
                    <option value="<?=$p['id']?>,<?=$o['id']?>">⬛ <?=$p['nombre']?>, <?=$o['nombre']?>
                <?php } ?>
          <?php } else {
                ?><option value="<?=$p['id']?>"><?=$icon?><?=$p['nombre']?><?php
                }
            }
            ?>
        </select>
        <button id="logout"><i class="icon-salir"></i></button>
    </header>
    <hr class="colorLines">
    <main>
        <form>
            <div class="grid">
                <button id="QR" class="s6 btn BGgrd2 feedback">Con QR</a>
                <button id="participar" class="s6 btn BGgrd1 feedback">Con dorsal</button>
            </div>
            <input id="dorsal" type="number" class="txt" maxlength="4" data-vkmaxlength="4" placeholder="Dorsal" disabled>
        </form>

        <div id="teclado" data-input="dorsal" class="tecladoNum">
            <button class="btn" data-key="7">7</button>
            <button class="btn" data-key="8">8</button>
            <button class="btn" data-key="9">9</button>
            <button class="btn" data-key="4">4</button>
            <button class="btn" data-key="5">5</button>
            <button class="btn" data-key="6">6</button>
            <button class="btn" data-key="1">1</button>
            <button class="btn" data-key="2">2</button>
            <button class="btn" data-key="3">3</button>
            <button class="btn" data-key="0">0</button>
            <button class="btn" data-key="-b"><i class="icon-backspace"></i></button>
            <button class="btn" data-key="-x"><i class="icon-error"></i></button>
        </div>

        <div id="qrContainer">
            <video id="qrVideo"></video>
            <button id="cancelQr" class="btn BGgrd3">Cancelar</button>
        </div>
    </main>
    <?php include('footer.php');?>

    <script type="module">
        import {START} from '/js/module-monitor.min.js?v4'
        START.init()
    </script>
</body>
</html>