<?php 
define('SECCION','Fotos'); 
global $INTL;

@$ev = \eventos\find(id:$_['eid'])[0];
if (!!!$ev) { header('Location:/404'); }
$pics = glob(ROOT_DIR.'/img/galerias/'.$ev['id'].'/*.webp');
?>
<!DOCTYPE html>
<html lang="es">
<?php include('head.php');?>

<body id="fotos">
    <?php include('header.php');?>

    <main>
        <h1 class="brush blue section"><?=$ev['nombre']?></h1>
        <h2 class="subtitle">(<?=$ev['lugar']?>)</h2>
        <h3 class="subtitle">
            <?php $INTL->setPattern("d"); ?>
            <?=$INTL->format( DateTime::createFromFormat('Y-m-d', $ev['fechaDesde']) )?> al
            <?php $INTL->setPattern("d 'de' MMMM 'de' u"); ?>
            <?=$INTL->format( DateTime::createFromFormat('Y-m-d', $ev['fechaHasta']) )?>
        </h3>
    <?php if ( empty($pics) ) {?>
        <p>No hay fotos</p>
    <?php } else { ?>
        <section class="galleryPics">
        <?php foreach ($pics as $k => $pic) {
            $thumb = ROOT_DIR.'/img/galerias/'.$ev['id'].'/thumbs/'.basename($pic);
            if (!file_exists($thumb)) {
                $img = \system\makeImgFromFile($pic);
                imagewebp( \system\createSquareThumb($img, 400), $thumb, 60);
            }
            ?>
            <img src="/img/galerias/<?=$ev['id']?>/thumbs/<?=basename($pic)?>">
        <?php } ?>
        </section>
        <?php } ?>
    </main>

    <script type="module">
        import {$, $$$, PICS} from '/js/module-web.min.js?v4'

        PICS.init()
    </script>

    <?php include('footer.php');?>
</body>
</html>