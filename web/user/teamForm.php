<?php 
define('SECCION', 'Registro');
if (!$USER->isLogged) { header('Location: /perfil/nuevo'); }
if (isset($_['id'])) {
    define('ID', $_['id']);
    if (!$eq = $USER->getEquipo(ID)) { header('Location:/403'); die; };
} else {
    define('ID', null);
}
?>
<!DOCTYPE html>

<html lang="es">
<?php include __DIR__ . '/../head.php'; ?>


<body id="registro">
    <?php include __DIR__ . '/../header.php';?>

    <main>
        <h1 class="brush pink section"><?=!!ID?'Modificar':'Nuevo'?> equipo</h1>
        <div class="screen active" data-screen="1">
            <form class="formUser yellow grid" id="formTeam" novalidate>
                <div class="s12 m6 input">
                    <label for="titulo">Titulo del equipo</label>
                    <input type="text" name="titulo" pattern="[0-9a-zá-úA-ZÁ-ÚÑñ ]{1,}" maxlength="50" placeholder="Nombre para el dorsal del equipo" value="<?=@$eq->titulo?>" required>
                </div>
                <div class="s12 m6 input">
                    <label for="nombre">Nombre y apelllidos</label>
                    <input type="text" name="nombre" maxlength="20" pattern="[a-zá-úA-ZÁ-ÚÑñ ]{1,}" placeholder="Nombre y apellidos del menor" value="<?=@$eq->nombre?>" required>
                </div>
                <div class="s12 m6 input">
                    <label for="nacimiento">Fecha de nacimiento</label>
                    <input type="date" name="nacimiento" value="<?=@$eq->nacimiento?>" required>
                </div>
                <div class="s12 m6 input">
                    <label for="bando">Equipo</label>
                    <select name="bando" id="bando">
                        <?php foreach (\bandos\listado() as $bando) { ?>
                            <option value="<?=$bando['id']?>" <?=$eq->bando['id']==$bando['id']?'selected':''?>><?=$bando['nombre']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="s12 center">
                    <input type="submit" class="send yellow" value="<?=!!ID?'Modificar':'Añadir'?>">
                </div>
                <?php if(!!ID) { ?>
                    <input type="button" id="showDelete" class="send red" value="Borrar">
                    <input type="hidden" name="id" value="<?=ID?>">
                <?php } ?>
            </form>
        </div>
        
        <?php if(!!ID) { ?>
        <div class="screen" data-screen="2">
            <form class="formUser formYellow grid" novalidate>
                <p>¿Estas seguro de que quieres borrar el equipo <strong><?=$eq->titulo?></strong>?</p>
                <p>Se borrarán todas las participaciones, reservas y resultados de este equipo.</p>
                <input type="button" id="sendDelete" class="send red" value="Borrar">
                <input type="button" id="cancel" class="send blue" value="Cancelar">
            </form> 
        </div>
        <?php } ?>
    </main>



    <script type="module">
        import {$, USER, may, changeScreen} from '/js/module-web.min.js'

        $('formTeam').onsubmit = (e)=> {
            e.preventDefault()
            if ( USER.checkTeamForm() ) { USER.sendTeamForm() }
            return false
        }

        <?php if(!!ID) { ?>
        $('showDelete').onclick = ()=> { changeScreen(2) }
        $('sendDelete').onclick = ()=> { USER.sendTeamDelete() }

        <?php } ?>
    </script>

    <?php include __DIR__ . '/../footer.php';?>
</body>

</html>