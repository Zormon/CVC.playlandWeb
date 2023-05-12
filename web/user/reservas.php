<?php if ( !$USER->isLogged ) { header('Location:/perfil/entrar'); } ?>

<?php define('SECCION','Mis reservas'); ?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>

<body id="reservas">
    <?php include(__DIR__.'/../header.php');?>

    <main>
        <h1 class="brush red section">Mis reservas</h1>
        <section id="misreservas" class="grid">
            <?php
            $reservas = $USER->reservas;
            if ( count($reservas) == 0 ) { ?>
                <p>No tienes reservas.</p>
            <?php } else { 
                foreach ($reservas as $r) { ?>
                    <article class="s12 m6 l4 blockCompact <?=CONST_COLORS[$r['id']%(CONST_COLORS_N-1)]?>" id="ev<?=$r['id']?>">
                        <div class="screen active" data-screen="1">
                            <p><span class="big">😀</span> <?=$r['eqNombre']?><br><small><?=$r['eqTitulo']?></small></p>
                            <p><span class="big">🧭</span> <?=$r['evNombre']?><br><small><?=$r['evLugar']?></small><br><small><?=$r['evDesde']?> ➡️ <?=$r['evHasta']?></small></p>
                            <p><span class="big">🎟️</span> <?=$r['enNombre']?><br><small><?=$r['enDescripcion']?></small></p>
                        <?php if ( $r['pagado'] == 'no' ) { ?>
                            <p class="center"><a class="flashButton blue" href="/perfil/reserva/pagar/<?=$r['id']?>">Pagar ahora (<?=$r['enPrecio']?>€)</a> <a class="flashButton red showCancelRes" data-rid="<?=$r['id']?>">Cancelar</a></p>
                        <?php } ?>
                        </div>

                        <div class="screen" data-screen="2">
                            <div class="grid center">
                                <div class="s12">
                                    <h2>¿Cancelar la reserva de <?=$r['eqNombre']?> para el evento <?=$r['evNombre']?></h2>
                                    <p>Vuelve a realizar la reserva después de cancelarla si deseas cambiar algún dato</p>
                                </div>
                                <div class="s6"><a class="flashButton red cancelRes" data-rid="<?=$r['id']?>">Sí</a></div>
                                <div class="s6"><a class="flashButton blue cancelCancel" data-rid="<?=$r['id']?>">No</a></div>
                            </div>
                        </div>
                    </article>
            <?php }
            } ?>
            <!--<p class="s12 center"><a class="flashButton blue" href="/perfil/reservas/nueva">Nueva reserva</a></p>-->
        </section>
    </main>

    <script type="module">
        import { RESERVAS } from '/js/module-web.min.js?v4'
        RESERVAS.init()
    </script>

    <?php include(__DIR__.'/../footer.php');?>
</body>
</html>