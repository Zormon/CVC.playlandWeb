<?php 
if (!$USER->isLogged) { header('Location: /perfil/nuevo'); }

define('SECCION', 'Reserva');
@$ev = \eventos\find(fields:'id',nombreURL:$_['evShortName'],tipo:'playland')[0];
if (@!!!$ev) { header('Location: /404'); }

$ev = \eventos\getInfo($ev['id']);
define('EV_COLOR', CONST_COLORS[$ev['evId']%(CONST_COLORS_N-1)] );
?>
<!DOCTYPE html>

<html lang="es">
<?php include __DIR__ . '/../head.php'; ?>


<body id="registro">
    <?php include __DIR__ . '/../header.php';?>

    <main>
        <h1 class="brush <?=EV_COLOR?> section">Nueva reserva</h1>
        <h2><?=$ev['evNombre']?></h2>
        <div class="screen active" data-screen="1">
            <form class="formUser yellow grid" id="formReserva" novalidate>
                <div class="s12 m6 input">
                    <label for="equipos">Equipos</label>
                    <select name="equipos" id="equipos" multiple required>
                        <?php foreach ($USER->equipos as $eq) { ?>
                            <option value="<?=$eq->id?>"><?=$eq->titulo?> | <?=$eq->nombre?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="s12 m6 input">
                    <label for="entrada">Entrada</label>
                    <select name="entrada" id="entrada" required>
                        <?php foreach ($ev['entradas'] as $en) { ?>
                            <option value="<?=$en['id']?>" data-precio="<?=$en['precioWeb']?>"><?=$en['nombre']?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="s12 m6 input">
                    <label for="dia">dia</label>
                    <select name="dia" id="dia" required>
                        <?php foreach ($ev['dias'] as $d) { ?>
                            <option value="<?=$d['date']?>"><?=$d['formatted']?></option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="s12 center">
                    <span id="precioTotal">Precio total: <strong>0 €</strong></span>
                </div>
                
                <div class="s12 center"><
                    <input type="submit" class="send yellow" value="Reservar">
                </div>
                <input type="hidden" name="evento" value="<?=$ev['evId']?>">
            </form>
        </div>
    </main>



    <script type="module">
        import {$, $$, RESERVAS, may, changeScreen} from '/js/module-web.min.js?3'

        const entradaEl = $('entrada')
        const equiposEl = $('equipos')

        function calculaPrecio() {
            const num = equiposEl.querySelectorAll('option:checked').length
            $('precioTotal').innerHTML = `Precio total: <strong>${num * entradaEl.options[entradaEl.selectedIndex].dataset.precio} €</strong>`
        }

        $('entrada').onchange = $('equipos').onchange = ()=> { calculaPrecio() }

        $('formReserva').onsubmit = (e)=> {
            e.preventDefault()
            if ( RESERVAS.checkReservaForm() ) { RESERVAS.sendReservaForm() }
            return false
        }
    </script>

    <?php include __DIR__ . '/../footer.php';?>
</body>

</html>