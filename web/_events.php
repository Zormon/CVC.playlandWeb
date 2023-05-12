<?php 
define('SECCION','Sobre Playland'); 
global $INTL;

$eventos = \eventos\listado(order:'fechaDesde DESC');
?>
<!DOCTYPE html>
<html lang="es">
<?php include('head.php');?>

<body id="eventos">
    <?php include('header.php');?>

    <main>
    <?php 
    @$ev = $eventos['present'][0];
    if (!!$ev) { ?>
        <section id="actualEvento">
            <article class="block red">
                <header><h1>¡Evento en curso!</h1></header>
                <h2><?=$ev['nombre']?></h2>
                <p><span class="big">🏆</span> <?=$ev['nombreLiga']?></p>
                <p><span class="big">🧭</span> <?=$ev['lugar']?></p>
                <?php $INTL->setPattern("EEEE d"); ?>
                <p><span class="big">📆</span> Del <?=$INTL->format( DateTime::createFromFormat('Y-m-d', $ev['fechaDesde']) )?>
                <?php $INTL->setPattern("EEEE d 'de' MMMM 'de' u"); ?>
                al <?=$INTL->format( DateTime::createFromFormat('Y-m-d', $ev['fechaHasta']) )?></p>
            </article>
        </section>
    <?php } ?>

    <?php if ($eventos['future']) { ?>
        <section id="proximosEventos">
            <header><h1 class="brush orange section">Próximos eventos</h1></header>
            <ul class="posterGrid">
                <?php foreach ($eventos['future'] as $ev) { 
                    $evImg = '_'; if ( is_file( ROOT_DIR.'/img/eventos/'.$ev['id'].'.webp' ) ) { $evImg = $ev['id']; } ?>
                    <li class="<?=CONST_COLORS[$ev['id']%(CONST_COLORS_N-1)]?>">
                        <a href="/eventos/<?=$ev['nombreURL']?>"><img src="/img/eventos/<?=$evImg?>.webp?<?=$ev['version']?>"></a>
                    </li>
                <?php } ?>
            </ul>
        </section>
    <?php } ?>

    <?php if ($eventos['past']) { ?>
        <section id="pasadosEventos">
            <header><h1 class="brush green section">Eventos pasados</h1></header>
            <ul class="posterGrid">
                <?php foreach ($eventos['past'] as $ev) { 
                    $evImg = '_'; if ( is_file( ROOT_DIR.'/img/eventos/'.$ev['id'].'.webp' ) ) { $evImg = $ev['id']; } ?>
                    <li class="<?=CONST_COLORS[$ev['id']%(CONST_COLORS_N-1)]?>">
                        <a href="/eventos/<?=$ev['nombreURL']?>"><img src="/img/eventos/<?=$evImg?>.webp"></a>
                    </li>
                <?php } ?>
            </ul>
        </section>
    <?php } ?>
    </main>

    <script type="module">
        import {$, $$$, EVENTS} from '/js/module-web.min.js?v4'

        EVENTS.init()
    </script>

    <?php include('footer.php');?>
</body>
</html>