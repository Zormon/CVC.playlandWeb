<?php
    global $DB;

function showProfile($p, $dupe=false) { ?>
        <div class="showCode">
            <?php if ($dupe) { ?><p>(Ya estabas registrado, esta es tu información)</p><?php } ?>
            <h1>Tu código: <?=$p['id']?></h1>
            <table>
                <tr><th>Nombre y apellidos:</th><td><?=$p['nombre']?></td></tr>
                <tr><th>DNI:</th><td><?=$p['DNI']?></td></tr>
                <tr><th>Email:</th><td><?=$p['email']?></td></tr>
                <tr><th>Teléfono:</th><td><?=$p['telefono']?></td></tr>
            </table>
        </div>
    <?php }
?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>
<body id="registro">
<?php include(__DIR__.'/../header.php');?>

    <main>
    <?php 
    $sql = "SELECT * FROM _onroad_registros WHERE evento='".$_POST['evID']."' AND ( DNI='".$_POST['DNI']."' OR email='".$_POST['email']."' )";
    $resDB = $DB->consulta($sql);

    // -- Si existe DNI o Email --
    if ( count($resDB)>0 ) {
        showProfile ( $resDB[0], true );

    //  - Si es nuevo registro --
    } else {
        $publi = @!!$_POST['publi']? 1 : 0;
        $tel = $_POST['telefono']? "'".$_POST['telefono']."'" : 'NULL';
        $sql = <<<CONSULTA
                INSERT INTO _onroad_registros (
                    evento, nombre, DNI,
                    email, telefono, publi, data
                ) VALUES (
                    {$_POST['evID']}, '{$_POST['nombre']}', '{$_POST['DNI']}',
                    '{$_POST['email']}', $tel, $publi, '{"beacon":1}'
                )
                CONSULTA;
        $DB->consulta($sql);

        $_POST['id'] = $DB->lastInsertId();
        showProfile( $_POST );
    }
    ?>
    </main>

<?php include(__DIR__.'/../footer.php');?>
</body>
</html>