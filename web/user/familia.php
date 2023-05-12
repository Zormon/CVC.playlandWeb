<?php if ( !$USER->isLogged ) { header('Location:/perfil/entrar'); } ?>

<?php define('SECCION','Mi perfil'); ?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>

<body id="perfil">
    <?php include(__DIR__.'/../header.php');?>

    <main>
        <section id="cuenta" class="grid">
            <article class="blockCompact green s12 m8 mof3">
                <header>Mi cuenta</header>
                <p><span class="big">😀</span> <?=$USER->nombre?></p>
                <p><span class="big">📧</span> <?=$USER->email?></p>
                <p><span class="big">📞</span> <?=$USER->telefono?></p>
                <p class="center"><a class="flashButton red" href="/perfil/modificar">Modificar</a></p>
            </article>
        </section>

        <h1 class="brush red section">Mis equipos</h1>
        <section id="equipos" class="grid">
            <?php
            if ( count($USER->equipos) == 0 ) { ?>
                <p>No tienes equipos.</p>
            <?php } else {
                global $INTL;
                foreach ($USER->equipos as $eq) { 
                    $INTL->setPattern("d 'de' MMMM 'de' YYYY");
                    $bdt = DateTime::createFromFormat('Y-m-d', $eq->nacimiento);
                    $nacimientoF = $INTL->format( $bdt );
                    ?>
                    <article class="s12 m6 l4 blockCompact <?=CONST_COLORS[$eq->id%(CONST_COLORS_N-1)]?>">
                        <header><?=$eq->titulo?></header>
                        <p><span class="big">😀</span> <?=$eq->nombre?></p>
                        <p><span class="big">🐣</span> <?=$eq->edad?> años <small>(<?=$nacimientoF?>)</small></p>
                        <?php if($eq->grupoEdad) { ?>
                            <p><span class="big">👥</span> <?=$eq->grupoEdad['nombre']?> </p>
                            <p><span class="big">🚩</span> <?=$eq->bando['nombre']?></p>
                        <?php } ?>
                        <p class="center"><span class="dorsal"><?=$eq->id?></span></p>
                        <?php
                        $qrB64 = \equipos\qrCodeB64($eq->id);
                        if ( $qrB64 ) { ?>
                        <p class="center"><img class="qrCode med" src="data:image/webp;base64,<?=$qrB64?>"></p>
                        <?php } ?>
                        <p class="center"><a class="flashButton red" href="/perfil/equipos/modificar/<?=$eq->id?>">Modificar</a></p>
                    </article>
                <?php }
            } ?>
            <p class="s12 center"><a class="flashButton blue" href="/perfil/equipos/nuevo">Añadir equipo</a></p>
        </section>
    </main>

    <?php include(__DIR__.'/../footer.php');?>
</body>
</html>