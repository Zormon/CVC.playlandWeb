<?php define('SECCION','Servicios de alquiler'); ?>
<!DOCTYPE html>
<html lang="es">
<?php include('head.php');?>

<body>
    <?php include('header.php');?>

    <main>
        <section>
            <h1 class="brush yellow section">Servicios de alquiler</h1>
            <p>Todos necesitamos un momento de ocio, de descanso de la rutina, de dejar a un lado los dispositivos electrónicos y disfrutar de la vida junto con la familia y seres que amamos.</p>
            <p>Por eso si estas organizando un evento o fiesta, y quieres diversión al máximo, en Playland podemos encargarnos de ello. Contamos con entretenimiento variado para todas las edades, incluso para los más pequeños de la casa.</p>
            <p>Entre las atracciones más destacables se encuentra el divertido cañón de espuma, hinchables para todas las edades, actividades deportivas, pantallas y sonidos.</p>
            <p>Tenemos todo lo necesario para que sea un acontecimiento especial para ti y para todos tus invitados. Donde lo más importante será la unión familiar y crear momentos inolvidables.</p>
            <p>Somos un equipo responsable y dedicado a ofrecer entretenimiento para familias o amigos, y además nos caracterizamos por tomar todas las medidas de seguridad oportunas.</p>
            <p>Si te interesa conocer más detalles sobre cada una de nuestras atracciones, sigue leyendo la información y contáctanos.</p>
            <p><strong>¡Haz de tu evento o fiesta un momento perfecto, donde la diversión no vaya a parar!</strong></p>
        </section>

        <section id="serviciosAlquiler" class="grid">
            <?php 
                $servicios = \serviciosAlquiler\find();

                foreach ($servicios as $s) { 
                    $color = CONST_COLORS[$s['id']%(CONST_COLORS_N-1)]; ?>
                    <article class="s12 m6 l4 blockCompact <?=$color?>">
                        <header><?=$s['nombre']?></header>
                        <figure class="imgHeader" style="background-image:url(/img/alquiler/<?=$s['id']?>.webp)"></figure>
                        <p><?=$s['intro']?></p>
                        <p class="center"><a class="flashButton <?=$color?>" href="/alquiler/<?=$s['nombreURL']?>">Ver más</a></p>
                    </article>
                <?php } ?>
        </section>
    </main>

    <?php include('footer.php');?>
</body>
</html>