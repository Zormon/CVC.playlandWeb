<?php
    global $DB;
    if ( !!!$EV = @$DB->consulta("SELECT * FROM eventos WHERE tipo='onroad' AND id=".$_['evId'])[0] ) { die('Evento onroad no encontrado'); }
    define('SECCION','Registro '. $EV['nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>
<body id="registro">
<?php include(__DIR__.'/../header.php');?>

    <main>
        <h1 class="brush pink section">Registro <?=$EV['nombre']?></h1>
        <form class="formUser pink grid" name="registro" method="POST" action="/onroad/registro">
            <div class="s12 m6 input">
                <label for="nombre">Nombre y apellidos</label>
                <input type="text" name="nombre" pattern="[a-zá-úA-ZÁ-ÚÑñ ]{1,}" maxlength="50" required>
            </div>
            <div class="s12 m6 input">
                <label for="DNI">DNI/NIE</label>
                <input type="text" name="DNI" maxlength="20" pattern="[a-zA-Z0-9]{1,}" required>
            </div>
            <div class="s12 m6 input">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" maxlength="80" required>
            </div>
            <div class="s12 m6 input">
                <label for="telefono">Teléfono</label>
                <input type="tel" name="telefono" maxlength="13" autocomplete="tel-national" pattern="[+]?[0-9]{9,12}">
            </div>
            <div class="s12 m6 input checkList">
                <input type="checkbox" name="publi" id="publi" checked>
                <label for="publi">Acepto recibir comunicaciones sobre Playland Gran Canaria</a></label>
            </div>
            <div class="s12 m6 input checkList">
                <input type="checkbox" id="legal" required>
                <label for="legal">Acepto las bases legales de participación</a></label>
            </div>
            <div class="s12 m6 mof4 l4 lof5">
                <input type="submit" class="send pink" value="Enviar">
            </div>
            <input type="hidden" name="evID" value="<?=$EV['id']?>">
        </form>
    </main>

<?php include(__DIR__.'/../footer.php');?>
</body>
</html>