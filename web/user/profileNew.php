<?php 
define('SECCION','Registro'); 
if ($USER->isLogged) { header('Location: /perfil'); }
?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>

<body id="registro">
    <?php include(__DIR__.'/../header.php');?>

    <main>
        <div class="screen active" data-screen="1">
            <h1 class="brush pink section">Registro</h1>
            <form class="formUser yellow grid" id="formProfile" novalidate>
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
                    <input type="tel" name="telefono" maxlength="13" autocomplete="tel-national" pattern="[+]?[0-9]{9,12}" required>
                </div>
                <div class="s12 m6 input">
                    <label for="pass">Contraseña</label>
                    <input type="password" maxlength="30" name="pass" autocomplete="new-password" required>
                </div>
                <div class="s12 m6 input">
                    <label for="passConfirm">Confirma contraseña</label>
                    <input type="password" maxlength="30" autocomplete="new-password" name="passConfirm" required>
                </div>
                <div class="s12 m6 input checkList">
                    <input type="checkbox" name="publi" id="publi" checked>
                    <label for="publi">Acepto recibir comunicaciones sobre Playland Gran Canaria</a></label>
                </div>
                <div class="s12 m6 input checkList">
                    <input type="checkbox" name="acepto" id="acepto">
                    <label for="acepto">Acepto las <a href="/legal">condiciones legales</a></label>
                </div>
                <div class="s12 m6 mof4 l4 lof5">
                    <input type="submit" class="send yellow" value="Enviar">
                </div>
            </form>
        </div>

        <div class="screen" data-screen="2">
            <article class="clean green">
                <header><h1><span class="FILLpink">⭐</span> ¡Genial! <span class="FILLpurple">⭐</span></h1></header>

                <p>Estás a un paso menos de poder participar.</p>
                <p>Se ha enviado un correo de confirmación a <em id="sentMail"></em>.</p>
            </article>

            <article class="blockInfo yellow">
                <p>Si no recibes el correo tras 5 minutos, haz las siguientes tareas por order:</p>
                <ul class="checkList">
                    <li>Comprueba la sección "Spam" o "correo no deseado" de tu cuenta de correo</li>
                    <li>Vuelve a realizar el registro</li>
                    <li><a href="/contacto">Contacta con nosotros</a></li>
                </ul>
            </article>
        </div>
    </main>

    <script type="module">
        import {$, USER} from '/js/module-web.min.js'

        $('formProfile').onsubmit = (e)=> {
            e.preventDefault()
            if ( USER.checkProfileForm(true) ) { USER.sendProfileForm(true) }

            return false
        }
    </script>

    <?php include(__DIR__.'/../footer.php');?>
</body>
</html>