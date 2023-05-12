<?php include('header.php');?>

<body class="app resumen cyan">
    <header>
        <nav class="BRDcyan">
            <button data-link="reservas" class="current">Resumen</button>
            <button data-link="reservas" class="CLRpink">Reservas</button>
            <button data-link="adultos" class="CLRpurple">Adultos</button>
            <button data-link="equipos" class="CLRgreen">Equipos</button>
            <button data-link="participaciones" class="CLRyellow">Participaciones</button>
            <button id="logout">-></button>
        </nav>
    </header>
    <main class="resumen">
    <?php if (EVENTO_ACTUAL) { ?>
        <section id="datos">
            <header><h1>Evento</h1></header>
            <table class="datos">
                <tr>
                    <td>Nombre:</td>
                    <td><?=EVENTO_ACTUAL['nombre']?> <small>(Id: <?=P_EVENTO_ACTUAL?>)</small></td>
                </tr>
                <tr>
                    <td>Lugar:</td>
                    <td><?=EVENTO_ACTUAL['lugar']?></td>
                </tr>
                <tr>
                    <td>Fecha:</td>
                    <td>Desde: <?=EVENTO_ACTUAL['fechaDesde']?> | Hasta: <?=EVENTO_ACTUAL['fechaHasta']?></td>
                </tr>
                <tr>
                    <td>Entradas vendidas:</td>
                    <td id="numReservas"></td>
                </tr>
                <tr>
                    <td>Participaciones:</td>
                    <td id="numParticipaciones"></td>
                </tr>
            </table>
        </section>
        <section id="rankings">
            <header><h1>Rankings</h1></header>
            <table id="rankingTable" class="datos">
                <tr>
                    <th>Equipo</th>
                    <th>Puntuacion</th>
                    <th>Edad</th>
                </tr>
            </table>
            <p>En construccion</p>
        </section>
    <?php } ?>
        <section id="plataforma">
            <header><h1>Plataforma</h1></header>
            <table class="datos">
                <tr>
                    <td>Adultos activos:</td>
                    <td id="numAdultosActivos"></td>
                </tr>
                <tr>
                    <td>Equipos:</td>
                    <td id="numEquiposActivos"></td>
                </tr>
            </table>
        </section>
    </main>
    <?php include('footer.php');?>
    <script type="module">
        import {$, fetchJson} from '/js/module-admin.min.js?v4'

        function showData() {
            fetchJson('/api/taquilla,resumen,general').then( resp => {
                if (resp._httpCode == 200) {
                    $('numAdultosActivos').textContent      = resp.data.plataforma.numAdultosActivos
                    $('numEquiposActivos').textContent      = resp.data.plataforma.numEquiposActivos

                    if (typeof resp.data.evento !== 'undefined') {
                        $('numReservas').textContent            = resp.data.evento.numReservas
                        $('numParticipaciones').textContent     = resp.data.evento.numParticipaciones    
                    }

                    if (typeof resp.data.rankings.ranking !== 'undefined') {
                        for (let item of resp.data.rankings.ranking) {
                            let tr = document.createElement('tr')
                            tr.innerHTML = `<td>${item.equipo}</td><td>${item.puntos}</td><td>${item.edad}</td>`

                            $('rankingTable').appendChild(tr)    
                        }
                    }
                }
                else { alert(`❌ Error al pedir los datos del resumen.<br>HTTP_CODE: ${data._httpCode}<br>RESPONSE: ${data.data}`); }
            })

        }

        showData()
        //setInterval(showData, 3000);
    </script>
</body>
</html>