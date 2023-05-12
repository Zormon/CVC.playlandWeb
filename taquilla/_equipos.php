<?php include('header.php');?>

<body class="app equipos green">
    <header>
        <nav class="BRDgreen">
            <button data-link="resumen" class="CLRcyan">Resumen</button>
            <button data-link="reservas" class="CLRpink">Reservas</button>
            <button data-link="adultos" class="CLRgreen">Adultos</button>
            <button data-link="equipos" class="current">Equipos</button>
            <button data-link="participaciones" class="CLRyellow">Participaciones</button>
            <button id="logout">-></button>
        </nav>
    </header>
    <main>
        <section class="actions">
            <input type="search" id="search" class="txt" placeholder="Buscar">
            <button id="add" class="btn BGgrd1">+ Nuevo equipo</button>

        </section>
        <section class="table equipos">
            <header>
                <span>Dorsal</span><span>Equipo</span><span>Participante</span><span>Nacimiento</span><span>Adulto</span><span>Pago hoy</span>
            </header>
            <ul id="lista"></ul>
        </section>
    </main>
    <?php include('footer.php');?>

    <script type="module">
        import {EQUIPOS} from '/js/module-taquilla.min.js?v4'
        import {$} from '/js/module-admin.min.js?v4'

        var equipos = new EQUIPOS( $('lista'), $('entryRow'), $('search') )
        equipos.printList(true)

        $('search').onkeyup = ()=> { equipos.printList(false) }
        $('add').onclick = ()=>{ equipos.modal() }
    </script>


    <script id="entryRow" type="text/template">
        <span>{{id}}</span><span>{{titulo}}</span><span>{{nombre}}</span><span>{{nacimiento}}</span><span>{{adulto}}</span><span>{{#pagado}}✔️{{/pagado}}{{^pagado}}❌{{/pagado}}</span>
    </script>

    <script id="entryModal" type="text/template">
        <form id="entryForm" class="grid">
            <div class="s12">
                <h3>Adulto reponsable</h3>
                <select name="adulto" id="formAdulto" required>
                    <option selected value="" disabled>Selecciona adulto</option>
                    <?php
                        $adultos = \adultos\listado();
                        foreach($adultos as $a) {
                            echo '<option value="'.$a['id'].'">'.$a['nombre'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="s12"><h3>Titulo equipo</h3><input class="txt" type="text" name="titulo" placeholder="Nombre equipo" value="{{titulo}}" required></div>
            <div class="s12"><h3>Nombre del menor</h3><input class="txt" type="text" name="nombre" placeholder="Nombre menor" value="{{nombre}}" required></div>
            <div class="s12"><h3>Nacimiento</h3><input class="txt" type="date" name="nacimiento" placeholder="Nacimiento" value="{{nacimientoISO}}" required></div>
            <div class="s12">
                <h3>Color</h3>
                <select class="txt" name="bando" id="formBando" required>
                    <?php
                        $bandos = \bandos\listado();
                        foreach($bandos as $b) {
                            echo '<option style="color:#'.$b['color'].';" value="'.$b['id'].'">'.$b['nombre'].'</option>';
                        }
                    ?>
                </select>
            </div>
            {{#id}}<input type="hidden" name="id" value="{{id}}">{{/id}}
        </form>
    </script>
</body>
</html>