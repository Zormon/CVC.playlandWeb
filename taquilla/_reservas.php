<?php include('header.php');?>

<body class="app reservas pink">
    <header>
        <nav class="BRDpink">
            <button data-link="resumen" class="CLRcyan">Resumen</button>
            <button data-link="reservas" class="current">Reservas</button>
            <button data-link="adultos" class="CLRpurple">Adultos</button>
            <button data-link="equipos" class="CLRgreen">Equipos</button>
            <button data-link="participaciones" class="CLRyellow">Participaciones</button>
            <button id="logout">-></button>
        </nav>
    </header>
    <main>
        <section class="actions">
            <input type="search" id="search" class="txt" placeholder="Buscar">
            <button id="quickAdd" class="btn BGgrd6">Inscripción rápida</button>
            <button id="add" class="btn BGgrd1">+ Nueva compra</button>
            <span id="count"></span>
        </section>
        <section class="table reservas">
            <header>
                <span>Equipo</span><span>Adulto</span><span>Dia</span><span>Entrada</span><span>Pagado</span>
            </header>
            <ul id="lista"></ul>
        </section>
    </main>
    <?php include('footer.php');?>
    <script type="module">
        import {RESERVAS} from '/js/module-taquilla.min.js?v4'
        import {$} from '/js/module-admin.min.js?v4'

        var reservas = new RESERVAS( $('lista'), $('entryRow'), $('search') )
        reservas.printList(true)


        $('search').onkeyup = (e)=> { reservas.printList(false) }
        $('add').onclick = ()=>{ reservas.modal() }
        $('quickAdd').onclick = ()=>{ reservas.quickBuyModal() }
    </script>


    <script id="entryRow" type="text/template">
        <span>{{equipo}} <?=$_CONFIG['debug']?'<small> ({{equipoId}})</small>':''?></span><span>{{adulto}}</span><span>{{diaF}}</span><span>{{entrada}}</span><span>{{pagado}}</span>
    </script>

    <script id="entryModal" type="text/template">
        <form id="entryForm" class="grid">
            <div class="s12">
                <h3>Equipo</h3>
                <select name="equipo" id="formEquipo" required>
                    <option selected value="" disabled>Selecciona equipo</option>
                    <?php
                        $equipos = \equipos\listado();
                        foreach($equipos as $e) {
                            echo '<option value="'.$e['id'].'">'.$e['titulo'].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="s12">
                <h3>Dia</h3>
                <?php
                    global $DB;
                    foreach(EVENTO_ACTUAL['dias'] as $d) {
                        echo '<span><input type="radio" id="dia'.$d['date'].'" name="dia" required value="'.$d['date'].'">';
                        echo '<label for="dia'.$d['date'].'">'.$d['formatted'].'</label></span>';
                    }
                ?>
            </div>
            <div class="s12">
                <h3>Entrada</h3>
                <?php
                $entradas = \entradas\listado(ids:EVENTO_ACTUAL['entradas']);
                    foreach($entradas as $e) {
                        echo '<span><input type="radio" id="ent'.$e['id'].'" name="entrada" required value="'.$e['id'].'">';
                        echo '<label for="ent'.$e['id'].'">'.$e['nombre'].'</label></span>';
                    }
                ?>
            </div>
            {{#id}}
            <input type="hidden" name="id" value="{{id}}">
            <div class="s12">
                <h3>Pagado</h3>
                <?php
                    $stats = $DB->getEnumValues('reservas','pagado');
                    foreach($stats as $s) {
                        echo '<span><input type="radio" id="s'.$s.'" name="pagado" required value="'.$s.'">';
                        echo '<label for="s'.$s.'">'.$s.'</label></span>';
                    }
                ?>
            </div>
            {{/id}}
        </form>
    </script>

    <script id="quickBuyModal" type="text/template">
        <form id="quickBuyForm">
            <div class="grid">
                <div class="s12">
                    <h3>Dia</h3>
                    <?php
                        global $DB;
                        foreach(EVENTO_ACTUAL['dias'] as $d) {
                            echo '<span><input type="radio" id="dia'.$d['date'].'" name="dia" required value="'.$d['date'].'">';
                            echo '<label for="dia'.$d['date'].'">'.$d['formatted'].'</label></span>';
                        }
                    ?>
                </div>
            </div>
            <fieldset class="grid">
                <legend>Adulto</legend>
                <div class="s12"><input class="txt" type="text" name="nombre" placeholder="Nombre y apellidos" value="{{nombre}}" required></div>
                <div class="s7"><input class="txt" type="text" name="DNI" placeholder="DNI" value="{{DNI}}" required></div>
                <div class="s5"><input class="txt" type="text" name="telefono" placeholder="Teléfono" value="{{telefono}}" required></div>
                <div class="s12"><input class="txt" type="text" name="email" placeholder="Correo Electrónico" value="{{email}}" required></div>
            </fieldset>
        <?php //Equipo 1 ?>
            <fieldset class="grid BRDcyan">
                <legend>Equipo 1</legend>
                <div class="s12"><input class="txt" type="text" name="equipo1" placeholder="Nombre equipo 1" required></div>
                <div class="s12"><input class="txt" type="text" name="participante1" placeholder="Nombre menor" required></div>
                <div class="s12"><input class="txt" type="date" name="nacimiento1" placeholder="Nacimiento" required></div>
                <div class="s12">
                    <select class="txt" name="bando1" id="bando1" required>
                        <?php
                            $bandos = \bandos\listado();
                            foreach($bandos as $b) {
                                echo '<option style="color:#'.$b['color'].';" value="'.$b['id'].'">'.$b['nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="s12">
                <?php
                    $checked = 'checked';
                    foreach($entradas as $e) {
                        echo '<span><input type="radio" id="ent1'.$e['id'].'" name="entrada1" '.$checked.' required value="'.$e['id'].'">';
                        echo '<label for="ent1'.$e['id'].'">'.$e['nombre'].'</label></span>';
                        $checked = '';
                    }
                ?>
                </div>
            </fieldset>

        <?php //Equipo 2 ?>
            <fieldset class="grid BRDpink">
                <legend>Equipo 2</legend>
                <div class="s12"><input class="txt" type="text" name="equipo2" placeholder="Nombre equipo 2"></div>
                <div class="s12"><input class="txt" type="text" name="participante2" placeholder="Nombre menor 2"></div>
                <div class="s12"><input class="txt" type="date" name="nacimiento2" placeholder="Nacimiento"></div>
                <div class="s12">
                    <select class="txt" name="bando2" id="bando2" required>
                        <?php
                            $bandos = \bandos\listado();
                            foreach($bandos as $b) {
                                echo '<option style="color:#'.$b['color'].';" value="'.$b['id'].'">'.$b['nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="s12">
                <?php
                    $checked = 'checked';
                    foreach($entradas as $e) {
                        echo '<span><input type="radio" id="ent2'.$e['id'].'" name="entrada2" '.$checked.' value="'.$e['id'].'">';
                        echo '<label for="ent2'.$e['id'].'">'.$e['nombre'].'</label></span>';
                        $checked = '';
                    }
                ?>
                </div>
            </fieldset>

        <?php //Equipo 3 ?>
            <fieldset class="grid BRDgreen">
                <legend>Equipo 3</legend>
                <div class="s12"><input class="txt" type="text" name="equipo3" placeholder="Nombre equipo 3"></div>
                <div class="s12"><input class="txt" type="text" name="participante3" placeholder="Nombre menor 3"></div>
                <div class="s12"><input class="txt" type="date" name="nacimiento3" placeholder="Nacimiento"></div>
                <div class="s12">
                    <select class="txt" name="bando3" id="bando3" required>
                        <?php
                            $bandos = \bandos\listado();
                            foreach($bandos as $b) {
                                echo '<option style="color:#'.$b['color'].';" value="'.$b['id'].'">'.$b['nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="s12">
                <?php
                    $checked = 'checked';
                    foreach($entradas as $e) {
                        echo '<span><input type="radio" id="ent3'.$e['id'].'" name="entrada3" '.$checked.' value="'.$e['id'].'">';
                        echo '<label for="ent3'.$e['id'].'">'.$e['nombre'].'</label></span>';
                        $checked = '';
                    }
                ?>
                </div>
            </fieldset>

        <?php //Equipo 4 ?>
            <fieldset class="grid BRDblue">
                <legend>Equipo 4</legend>
                <div class="s12"><input class="txt" type="text" name="equipo4" placeholder="Nombre equipo 4"></div>
                <div class="s12"><input class="txt" type="text" name="participante4" placeholder="Nombre menor 4"></div>
                <div class="s12"><input class="txt" type="date" name="nacimiento4" placeholder="Nacimiento"></div>
                <div class="s12">
                    <select class="txt" name="bando4" id="bando4" required>
                        <?php
                            $bandos = \bandos\listado();
                            foreach($bandos as $b) {
                                echo '<option style="color:#'.$b['color'].';" value="'.$b['id'].'">'.$b['nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="s12">
                <?php
                    $checked = 'checked';
                    foreach($entradas as $e) {
                        echo '<span><input type="radio" id="ent4'.$e['id'].'" name="entrada4" '.$checked.' value="'.$e['id'].'">';
                        echo '<label for="ent4'.$e['id'].'">'.$e['nombre'].'</label></span>';
                        $checked = '';
                    }
                ?>
                </div>
            </fieldset>
            
        </form>
    </script>
</body>
</html>