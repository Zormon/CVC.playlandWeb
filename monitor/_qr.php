<?php
    require(__DIR__.'/../lib/class.monitor.php');
    
    // ======================== LOGIN PROCESS ====================
    $MONITOR = new monitor();
    if (!$MONITOR->isLogged) { header('Location: /monitor/login'); exit(); }
?>

<?php
include('header.php');
?>

<body class="app qr">
    <header>
        <?=$MONITOR->prueba['nombre']?>
        <button id="logout"><i class="icon-salir"></i></button>
    </header>
    <hr class="colorLines">
    <main>
        <a href="/monitor/" class="s6 btn BGgrd3 feedback">Introducir DNI</a>
        <br>
        <video id="qrVideo"></video>
    </main>
    <?php include('footer.php');?>

    <script type="module">
        import {$, fetchJson} from '/js/module-admin.min.js?v4'
        import QrScanner from '/js/qr-scanner.min.js'

        const qrScan = new QrScanner( $('qrVideo'), (res) => {
            if ( res.data.substr(0, 2) == 'e:' ) { // Es un QR de equipo
                const eData = res.data.substr(2).split(',')
                qrScan.stop()
                sendQr(eData)
            }
        },{returnDetailedScanResult: true})
        qrScan.start();

        function sendQr(data) {
            fetchJson('/api/monitor,qr', {eq:data[0], qr:data[1]}).then( (resp)=> {
                switch (resp.status) {
                    case 0x0000: location = '/monitor/participar/'+resp.data.equipo; break;
                    case 0x0424: alert('ERROR: QR de equipo inválido'); break;
                    case 0x0422: alert('ERROR: El equipo no ha pagado'); break;
                    case 0x0423: alert('ERROR: El equipo no tiene más intentos'); break;
                    default: alert(`ERROR: ${resp.data}`); break;
                }
            })
        }
    </script>
</body>
</html>