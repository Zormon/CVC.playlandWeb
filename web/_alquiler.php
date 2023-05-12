<?php 
    define('SECCION','Alquiler'); 
    @$s = \serviciosAlquiler\find(nombreURL:$_['sNombre'])[0];
    if (!!!$s) { header('Location:/404'); }
    $color = CONST_COLORS[$s['id']%(CONST_COLORS_N-1)];
?>
<!DOCTYPE html>
<html lang="es">
<?php include('head.php');?>

<body>
    <?php include('header.php');?>

    <main class="banner">
        <h1 class="brush <?=$color?> section"><?=$s['nombre']?></h1>
        <div class="alquiler">
            <div id="alPicture"><img src="/img/alquiler/<?=$s['id']?>.webp?<?=$s['version']?>"></div>
            <div id="alVideo">
                <?php if ( is_file( ROOT_DIR.'/video/alquiler/'.$s['id'].'.mp4' )) { ?>
                <video src="/video/alquiler/<?=$s['id']?>.mp4"></video>
                <?php } ?>
            </div>
            <div id="alText"><?=$s['texto']?></div>
        </div>
    </main>

    <?php include('footer.php');?>
</body>
</html>