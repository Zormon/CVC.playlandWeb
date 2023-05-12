<!DOCTYPE html>
<html lang="es">
<?php include('head.php');?>

<body id="home">
    <?php include('header.php');?>

    <main>
        <section id="slideshow">
            <div id="slider" class="slides">
                <div style="background-image: url(/img/home/slides/slide0.webp)"></div>
                <div style="background-image: url(/img/home/slides/slide1.webp)"></div>
                <div style="background-image: url(/img/home/slides/slide2.webp)"></div>
            </div>
            <h1>¿Buscando diversión?</h1>
            <?php
            $USER = new user();
            if ($USER->isLogged) { ?>
                <a class="flashButton red" href="/eventos">¡Apúntate!</a>
            <?php } else { ?>
                <div class="grid">
                    <a class="s12 flashButton red" href="https://www.youtube.com/watch?v=OC0WDq-Hn0s">Conócenos</a>
                </div>
            <?php } ?>
        </section>

        <section id="intro">
            <h2 class="subtitle CLRpink">La familia no es importante, lo es todo</h1>
            <h1 class="brush pink section">¡Bienvenidos a Playland!</h1>
            <p>Has llegado al sitio ideal si lo que quieres es pasar un día diferente lleno de emoción y un buen ambiente, junto a familiares, amigos o compañeros de trabajo.</p>
            <p>En Playland nos dedicamos a hacer tus momentos de entretenimiento lo más especial posible, donde tu única preocupación sea divertirte al máximo.</p>
            <p>Por eso te ofrecemos las mejores atracciones, adaptándonos siempre a tus preferencias.</p>
        </section>

        <section id="destacados" class="grid">
            <figure class="s12 m6 l4 imgBlock red">
                <img src="/img/home/destacados/dest1.webp" alt="Adrenalina">
                <div class="info slideUp">
                    <h1>Adrenalina</h1>
                    <div class="reveal">
                        <h2>Desahoga tus emociones</h2>
                        <a class="flashButton orange" href="/sobre-playland">Saber más</a>
                    </div>
                </div>
            </figure>
            <figure class="s12 m6 l4 imgBlock pink">
                <img src="/img/home/destacados/dest2.webp" alt="Familia">
                <div class="info slideUp">
                    <h1>Familia</h1>
                    <div class="reveal">
                        <h2>Fomentamos la importancia de la familia</h2>
                        <a class="flashButton red" href="/sobre-playland">Saber más</a>
                    </div>
                </div>
            </figure>
            <figure class="s12 m6 l4 imgBlock orange">
                <img src="/img/home/destacados/dest3.webp" alt="Deporte">
                <div class="info slideUp">
                    <h1>Deporte</h1>
                    <div class="reveal">
                        <h2>Nunca hacer deporte fue tan divertido</h2>
                        <a class="flashButton blue" href="/sobre-playland">Saber más</a>
                    </div>
                </div>
            </figure>
            <figure class="s12 m6 l4 imgBlock green">
                <img src="/img/home/destacados/dest4.webp" alt="Diversión">
                <div class="info slideUp">
                    <h1>Diversión</h1>
                    <div class="reveal">
                        <h2>Sumérgete en un mar de diversión </h2>
                        <a class="flashButton orange" href="/sobre-playland">Saber más</a>
                    </div>
                </div>
            </figure>
            <figure class="s12 m6 l4 imgBlock yellow">
                <img src="/img/home/destacados/dest5.webp" alt="Hinchables">
                <div class="info slideUp">
                    <h1>Hinchables</h1>
                    <div class="reveal">
                        <h2>Saltar y jugar para todas las edades</h2>
                        <a class="flashButton pink" href="/sobre-playland">Saber más</a>
                    </div>
                </div>
            </figure>
            <figure class="s12 m6 l4 imgBlock blue">
                <img src="/img/home/destacados/dest6.webp" alt="Equipos">
                <div class="info slideUp">
                    <h1>Equipos</h1>
                    <div class="reveal">
                        <h2>Crea tu propio equipo con familia o amigos</h2>
                        <a class="flashButton yellow" href="/sobre-playland">Saber más</a>
                    </div>
                </div>
            </figure>
        </section>

        <section id="principios">
            <h1 class="brush green section">Nuestros principios</h1>
            <p>Hemos creado un concepto de competición familiar, en el cual padre, madre o acompañante, junto a su hijo forman un equipo. En Playland Gran Canaria tenemos fuertes valores, de los que nos guiamos para hacer que nuestro trabajo sea sinónimo de felicidad para nuestros clientes. </p>

            <div class="grid">
                <div class="BGpink iconBlock s6 m4">
                    <i class="icon-tiempo"></i>
                    <p>Tiempo de calidad</p>
                </div>
                <div class="BGgreen iconBlock s6 m4">
                    <i class="icon-familia"></i>
                    <p>Familia</p>
                </div>
                <div class="BGblue iconBlock s6 m4">
                    <i class="icon-companeros"></i>
                    <p>Compañerismo</p>
                </div>
                <div class="BGyellow-dark iconBlock s6 m4">
                    <i class="icon-diversion"></i>
                    <p>Diversión</p>
                </div>
                <div class="BGred iconBlock s6 m4">
                    <i class="icon-deporte"></i>
                    <p>Deporte</p>
                </div>
                <div class="BGorange iconBlock s6 m4">
                    <i class="icon-salud"></i>
                    <p>Bienestar y salud</p>
                </div>
            </div>

            <img src="/img/home/principios.webp" alt="Principios">
        </section>

        <section id="visitaGaleria">
            <h1 class="brush blue section">Visita nuestra galería</h1>
            <p>¿Ya has participado y quieres ver tus fotos o las de tu familia? o simplemente ¿Quieres ver lo bien que nos lo pasamos todos en Playland? Aquí les dejamos todas las fotos de nuestro paso por los diferentes eventos que hemos realizado al rededor de la isla.</p>

            <div class="imgBlock blue">
                <img src="/img/home/galeria.webp" alt="Visita nuestra galería">
                <div class="info fade">
                    <div class="reveal">
                        <a class="flashButton red" href="/fotos">Ver galería</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="patrocinadores">
            <h1 class="brush red section">Nuestros patrocinadores</h1>
            <ul id="logos" class="grid">
                <li class="s4 m3"><img src="/img/home/patrocinadores/cvc.webp" alt="Comunicación Visual Canarias"></li>
                <li class="s4 m3"><img src="/img/home/patrocinadores/cash.webp" alt="Cash converters"></li>
                <li class="s4 m3"><img src="/img/home/patrocinadores/dunas.webp" alt="Hotel Dunas"></li>
                <li class="s4 m3"><img src="/img/home/patrocinadores/galdar.webp" alt="Ayundamiento de Galdar"></li>
                <li class="s4 m3"><img src="/img/home/patrocinadores/leotron.webp" alt="Leotron"></li>
                <li class="s4 m3"><img src="/img/home/patrocinadores/maspalomas.webp" alt="Maspalomas"></li>
                <li class="s4 m3"><img src="/img/home/patrocinadores/spar.webp" alt="Spar"></li>
                <li class="s4 m3"><img src="/img/home/patrocinadores/tirma.webp" alt="Tirma"></li>
            </ul>
        </section>
    </main>

    <script type="module">
        import { $, $$$, slideShow } from "/js/module-web.min.js";

        var slideshow = new slideShow('slider', 4)
        slideshow.start()
    </script>

    <?php include('footer.php');?>
</body>
</html>