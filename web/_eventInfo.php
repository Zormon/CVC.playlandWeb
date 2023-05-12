<?php 
define('SECCION','Eventos'); 
global $INTL;

@$ev = \eventos\find(nombreURL:$_['evNombre'])[0];
if (@!!!$ev) { header('Location: /404'); }
$ev = \eventos\getInfo($ev['id']);
define('EV_COLOR', CONST_COLORS[$ev['evId']%(CONST_COLORS_N-1)] );
?>
<!DOCTYPE html>
<html lang="es">
<?php include('head.php');?>

<body>
    <?php include('header.php');?>
    <main>
        <h1 id="evHeader" class="brush <?=EV_COLOR?>"><?=$ev['evNombre']?></h1>
        <div class="eventInfo">
            <img id="imgPoster" src="/img/eventos/<?=$ev['img']?>.webp?<?=$ev['version']?>">
            <?php  if ($ev['timeframe'] == \eventos\timeframe::future || $ev['timeframe'] == \eventos\timeframe::present) { ?>
                <?php  if ($ev['evTipo'] == 'playland') { ?>
                    <a id="evAction" href="/perfil/reservas/nueva/<?=$ev['evNombreURL']?>" class="btn BGgrd1">Reserva</a>
                <?php } else if (@!!$ev['evData']->actionUrl) { ?>
                    <a id="evAction" href="<?=$ev['evData']->actionUrl?>" class="btn BGgrd1">Regístrate</a>
                <?php } ?>
            <?php } ?>
            <?php  if ($ev['timeframe'] == \eventos\timeframe::past) { ?>
            <a id="evGallery" href="/fotos/<?=$ev['evId']?>" class="btn BGgrd3">Galería de fotos</a>
            <?php } ?>
            <section id="data" class="BG<?=EV_COLOR?>-light TXT<?=EV_COLOR?>-dark">
                <?php
                if ($ev['liNombre']) { ?>
                    <h4>🏆 Liga</h4>
                    <p><?=$ev['liNombre']?>
                <?php } ?>
                <h4>🧭 Lugar</h4>
                <p><?=$ev['evLugar']?><br><a class="hidden-m hidden-l TXT<?=EV_COLOR?>-dark" href="#map">Ir al mapa ⬇️</a>
                </p>
                <h4>📆 Fecha</h4>
				<?php if ($ev['evDesdeF'] == $ev['evHastaF']) { ?>
				<p><time datetime="<?=$ev['evDesde']?>"><?=$ev['evDesdeF']?></time></p>
				<?php } else { ?>
                <p>Del <time datetime="<?=$ev['evDesde']?>"><?=$ev['evDesdeF']?></time> al <time datetime="<?=$ev['evHasta']?>"><?=$ev['evHastaF']?></time></p>
				<?php } ?>
            </section>
            <section id="info">
                <h4>📃 Información general</h4>
                <?=$ev['evInfo']?>
            </section>
            <aside id="map">
                <iframe src="https://maps.google.com/maps?q=<?=$ev['evLatitud']?>,<?=$ev['evLongitud']?>&hl=es&z=14&amp;output=embed"></iframe>
            </aside>
        </div>
    </main>

    <script type="module">
        import {$, $$$, EVENTS} from '/js/module-web.min.js?v4'
    </script>

    <?php include('footer.php');?>
</body>
</html>