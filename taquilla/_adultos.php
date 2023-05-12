<?php include('header.php');?>

<body class="app adultos purple">
    <header>
        <nav class="BRDpurple">
            <button data-link="resumen" class="CLRcyan">Resumen</button>
            <button data-link="reservas" class="CLRpink">Reservas</button>
            <button data-link="adultos" class="current">Adultos</button>
            <button data-link="equipos" class="CLRgreen">Equipos</button>
            <button data-link="participaciones" class="CLRyellow">Participaciones</button>
            <button id="logout">-></button>
        </nav>
    </header>
    <main>
        <section class="actions">
            <input type="search" id="search" class="txt" placeholder="Buscar">
            <button id="add" class="btn BGgrd1">+ Nuevo adulto</button>

        </section>
        <section class="table adultos">
            <header>
                <span>DNI</span><span>Nombre</span><span>Email</span><span>Teléfono</span><span>Estado</span>
            </header>
            <ul id="lista"></ul>
        </section>
    </main>
    <?php include('footer.php');?>

    <script type="module">
        import {ADULTOS} from '/js/module-taquilla.min.js?v4'
        import {$} from '/js/module-admin.min.js?v4'

        var adultos = new ADULTOS( $('lista'), $('entryRow'), $('search') )
        adultos.printList(true)

        $('search').onkeyup = ()=> { adultos.printList(false) }
        $('add').onclick = ()=>{ adultos.modal() }
    </script>


    <script id="entryRow" type="text/template">
        <span>{{DNI}}</span><span>{{nombre}}</span><span>{{email}}</span><span>{{telefono}}</span><span>{{estado}}</span>
    </script>

    <script id="entryModal" type="text/template">
        <form id="entryForm" class="grid">
            <div class="s12"><input class="txt" type="text" name="nombre" placeholder="Nombre y apellidos" value="{{nombre}}" required></div>
            <div class="s7"><input class="txt" type="text" name="DNI" placeholder="DNI" value="{{DNI}}" required></div>
            <div class="s5"><input class="txt" type="text" name="telefono" placeholder="Teléfono" value="{{telefono}}"></div>
            <div class="s12"><input class="txt" type="text" name="email" placeholder="Correo Electrónico" value="{{email}}"></div>
            {{#id}}
            <input type="hidden" name="id" value="{{id}}">
            <div class="s12">
                <?php
                global $DB;
                $estados = $DB->getEnumValues('adultos', 'estado');
                    foreach($estados as $e) {
                        echo '<span><input type="radio" id="est'.$e.'" name="estado" required value="'.$e.'">';
                        echo '<label for="est'.$e.'">'.$e.'</label></span>';
                    }
                ?>
            </div>
            {{/id}}
        </form>
    </script>
</body>
</html>