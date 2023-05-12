<?php include('header.php');?>

<body class="app participaciones yellow">
    <header>
        <nav class="BRDyellow">
            <button data-link="resumen" class="CLRcyan">Resumen</button>
            <button data-link="reservas" class="CLRpink">Reservas</button>
            <button data-link="adultos" class="CLRpurple">Adultos</button>
            <button data-link="equipos" class="CLRgreen">Equipos</button>
            <button data-link="participaciones" class="current">Participaciones</button>
            <button id="logout">-></button>
        </nav>
    </header>
    <main>
        <section class="actions">
            <input type="search" id="search" class="txt" placeholder="Buscar">
            <button id="add" class="btn BGgrd1">+ Nueva participación</button>
        </section>
        <section class="table participaciones">
            <header>
                <span>Equipo</span>
                <span>Fecha</span>
                <span>Prueba</span>
                <span>Resultado</span>
            </header>
            <ul id="lista"></ul>
        </section>
    </main>
    <?php include('footer.php');?>
    <script type="module">
        import {PARTICIPACIONES} from '/js/module-taquilla.min.js?v4'
        import {$} from '/js/module-admin.min.js?v4'

        var participaciones = new PARTICIPACIONES( $('lista'), $('entryRow'), $('search') )
        participaciones.printList(true)

        $('search').onkeyup = (e)=> { participaciones.printList(false) }
        $('add').onclick = ()=>{ participaciones.modal() }
    </script>


    <script id="entryRow" type="text/template">
        <span>{{equipo}}</span><span>{{fecha}}</span><span>{{prueba}}</span><span>{{resultadoISO}}</span>
    </script>

    <script id="entryModal" type="text/template">
        <form id="entryForm" class="grid">
            <div class="s12">
                <label for="formEquipo">Equipo</label>
                <select name="equipo" id="formEquipo" required>
                    <option selected value="" disabled>Selecciona equipo</option>
                    <?php
                        $equipos = \equipos\listado();
                        foreach($equipos as $e) {
                            echo '<option value="'.$e['id'].'">'.$e['nombre'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="s12">
                <label for="formPrueba">Prueba</label>
                <select name="prueba" id="formPrueba" required>
                    <option selected value="" disabled>Selecciona prueba</option>
                    <?php
                        $pruebas = \pruebas\listado();
                        foreach($pruebas as $p) {
                            echo '<option value="'.$p['id'].'">'.$p['nombre'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="s12"><input class="txt" type="number" name="resultado" placeholder="Resultado" value="{{resultado}}" required></div>
        </form>
    </script>
</body>
</html>