<?php 
define('SECCION','Galerías'); 
global $INTL;

$eventos = \eventos\listado();
?>
<!DOCTYPE html>
<html lang="es">
<?php include('head.php');?>

<body id="galerias">
    <?php include('header.php');?>

    <main>
        <h1 class="brush blue section">Galerías de fotos</h1>
        <section class="galleryList">
            <?php
            foreach ($eventos['past'] as $ev) {
                ?>
                <a href="/fotos/<?=$ev['id']?>" style="background-image:url(/img/eventos/<?=$ev['img']?>.webp)" class="<?=CONST_COLORS[$ev['id']%(CONST_COLORS_N-1)]?>">
                    <div class="info">
                        <h2><?=$ev['nombre']?></h2>
                        <?php $INTL->setPattern("d MMMM u"); ?>
                        <p><time datetime="$ev['fechaDesde']"><?=$INTL->format( DateTime::createFromFormat('Y-m-d', $ev['fechaDesde']) )?></time>
                        <br><?=$ev['lugar']?></p>
                    </div>
                </a>
                <?php
            }
            ?>
        </section>
    </main>
    
    <?php include('footer.php');?>
</body>
</html>