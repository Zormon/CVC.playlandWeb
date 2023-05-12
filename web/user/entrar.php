<?php   
if ( $USER->isLogged ) { header('Location:/perfil'); }
define('SECCION','Entrar'); 
?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>

<body id="entrar">
    <?php include(__DIR__.'/../header.php');?>

    <main>
        <h1 class="brush green section">Entrar</h1>
        <form class="formUser yellow grid" id="formLogin">
            <div class="s12 m6 input">
                <label for="login">DNI/NIE o correo electrónico</label>
                <input type="text" name="login" maxlength="50" required>
            </div>
            <div class="s12 m6 input">
                <label for="pass">Contraseña</label>
                <input type="password" maxlength="30" name="pass" required>
            </div>
            <div class="s12 center">
                <input type="submit" class="send yellow" value="Acceder">
            </div>
        </form>
    </main>

    <script type="module">
        import {$, USER} from '/js/module-web.min.js'

        $('formLogin').onsubmit = (e)=> {
            e.preventDefault()
            USER.sendLoginForm()

            return false
        }
    </script>

    <?php include(__DIR__.'/../footer.php');?>
</body>
</html>