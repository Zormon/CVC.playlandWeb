<header id="mainHeader">
    <button id="toggle-mainMenu"><i class="icon-menu big"></i></button>
    <img id="mainLogo" src="/img/playlandLogo.webp" alt="Playland Logo">
    <nav id="mainMenu">
        <a href="/">Inicio</a>
        <!--<a href="/news">Noticias</a>-->
        <a href="/sobre-playland">Sobre Playland</a>
        <a href="/eventos">Eventos</a>
        <a href="/fotos">Fotos</a>
        <!--<a href="/clasificaciones">Clasificaciones</a>-->
        <a href="/alquiler">Servicios de alquiler</a>
    </nav>
    
    <?php
    global $_CONFIG;
    if ( $_CONFIG['profileMenu'] && isset($USER) ) {
        $redFlag = false;
        if ($USER->isLogged) {
            $nresSinPagar = count( $USER->getReservasSinPagar() );
            if ($nresSinPagar > 0)  { $resBadge = '<span class="badge red">'.$nresSinPagar.'</span>'; $redFlag = true; }
            else                    { $resBadge = ''; }
        }
        ?>
        <button id="toggle-profileMenu" class="<?=$redFlag?'flag':''?>"><i class="icon-perfil big"></i></button>
        <nav id="profileMenu">
            <?php if ($USER->isLogged) { ?>
                <a href="/perfil">Mi familia</a>
                <a href="/perfil/reservas">Mis reservas<?=$resBadge?></a>
                <a href="/perfil/resultados">Mis resultados</a>
                <a id="userUnlog">Salir</a>
            <?php } else { ?>
            <a href="/perfil/nuevo">Regístrate</a>
            <a href="/perfil/entrar">Entrar</a>
            <?php } ?>
        </nav>    
    <?php } ?>
    
    <hr class="colorLines">
</header>

<script type="module">
    import {WEB} from '/js/module-web.min.js';
    WEB.init()
</script>