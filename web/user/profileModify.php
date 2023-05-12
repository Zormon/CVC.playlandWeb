<?php define('SECCION','Modificar perfil'); ?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>

<body id="registro">
    <?php include(__DIR__.'/../header.php');?>
    <main>
        <h1 class="brush pink section">Modificar perfil</h1>
        <form class="formUser yellow grid" id="formProfile" novalidate>
            <div class="s12 m6 input">
                <label for="nombre">Nombre y apellidos</label>
                <input type="text" name="nombre" pattern="[a-zá-úA-ZÁ-ÚÑñ ]{1,}" maxlength="50" value="<?=$USER->nombre?>" required>
            </div>
            <div class="s12 m6 input">
                <label for="DNI">DNI/NIE</label>
                <input type="text" name="DNI" maxlength="20" pattern="[a-zA-Z0-9]{1,}" value="<?=$USER->DNI?>" required>
            </div>
            <div class="s12 m6 input">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" maxlength="80" value="<?=$USER->email?>" required>
            </div>
            <div class="s12 m6 input">
                <label for="telefono">Teléfono</label>
                <input type="tel" name="telefono" maxlength="13" autocomplete="tel-national" pattern="[+]?[0-9]{9,12}" value="<?=$USER->telefono?>" required>
            </div>
            <div class="s12 m6 input">
                <label for="pass">Contraseña</label>
                <input type="password" maxlength="30" name="pass" autocomplete="new-password" placeholder="Dejar vacía para no cambiar">
            </div>
            <div class="s12 m6 input">
                <label for="passConfirm">Confirma contraseña</label>
                <input type="password" maxlength="30" autocomplete="new-password" name="passConfirm">
            </div>
            <div class="s12 m6 input checkList">
                <input type="checkbox" name="publi" id="publi" <?=$USER->publi?'checked':''?>>
                <label for="publi">Acepto recibir comunicaciones sobre Playland Gran Canaria</a></label>
            </div>
            <div class="s12 m6 mof4 l4 lof5">
                <input type="submit" class="send yellow" value="Modificar">
            </div>
        </form>
    </main>

    <script type="module">
        import {$, USER} from '/js/module-web.min.js'

        $('formProfile').onsubmit = (e)=> {
            e.preventDefault()
            if ( USER.checkProfileForm(false) ) { USER.sendProfileForm(false) }

            return false
        }
    </script>

    <?php include(__DIR__.'/../footer.php');?>
</body>
</html>