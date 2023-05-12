<?php
    global $DB;
    if ( !!!$EV = @$DB->consulta("SELECT * FROM eventos WHERE tipo='onroad' AND nombreURL='".$_['evURL']."'")[0] )      { header('Location:/404'); }
    if ( !!!$BC = @$DB->consulta("SELECT * FROM _onroad_beacons WHERE evento=".$EV['id']." AND code='".$_['code']."'")[0] )   { header('Location:/404'); }
    define('SECCION','Puesto '. $BC['nombre']);
    define('BC_COLOR', CONST_COLORS[$EV['id']%(CONST_COLORS_N-1)] );
?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>
<body id="beacon">
<?php include(__DIR__.'/../header.php');?>

    <main>
        <h1 class="brush <?=BC_COLOR?> TXT<?=BC_COLOR?>-dark"><?=$BC['nombre']?></h1>
        <section id="beaconInfo"><?=$BC['info']?></section>
        <section id="beaconPin">
            <div class="screen active" data-screen="1">
                <h2>Confirma tu código</h2>
                <form id="beaconForm">
                    <label for="code">Código</label>
                    <input type="number" name="code" id="code" min="10" max="9999" required>
                    <input type="submit" class="btn BGgrd3" value="Confirmar">
                </form>
            </div>
            <div class="screen" data-screen="2">
                <h2>✔️ Código confirmado</h2>
            </div>
            <div class="screen" data-screen="3">
                <h2>❌ Error</h2>
                <p></p>
            </div>
        </section>
    </main>

    <script type="module">
        import {$, $$, fetchJson, changeScreen} from '/js/module-web.min.js'

        $('beaconForm').onsubmit = async (e)=> {
            e.preventDefault()
            const f = e.currentTarget
            const code = $('code').value

            let resp = await fetchJson(`/onroad/api/beacon,<?=$EV['id']?>,<?=$BC['num']?>,${code}`)
            switch (resp._httpCode) {
                case 200:
                    changeScreen(2, $('beaconPin') )
                break;
                case 400:
                    alert('El codigo es incorrecto')
                break;
                case 409:
                    const SN = 3; const p = $$(`#beaconPin > .screen[data-screen="${SN}"] > p`)
                    p.innerHTML = `Tu próximo objetivo es el punto número <em id="nextBC">${resp.data.nextBeacon}</em>`
                    changeScreen(SN, $('beaconPin') )
                break;
            }

            return false
        }
    </script>

<?php include(__DIR__.'/../footer.php');?>
</body>
</html>