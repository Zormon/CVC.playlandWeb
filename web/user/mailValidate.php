<?php
$data = explode( ':', \system\descifrar( @hex2bin($_['token']) ) );
?>

<?php define('SECCION','Registro'); ?>
<!DOCTYPE html>
<html lang="es">
<?php include(__DIR__.'/../head.php');?>

<body id="registro">
    <?php include(__DIR__.'/../header.php');?>
    <?php //TODO: Bad request and HTTP errors ?>
    <main>
        <?php if ( @$data[1] != 'x' ) { // Tampered petition ?>
            <article class="blockInfo red">
                <header><h1>Peticion inválida</h1></header>
                <p>Asegúrate de haber accedido a la dirección que te hemos enviado</p>
        <?php } else { 
            $data[2] = hexdec($data[2]);
            define('ID', $data[2]);
            switch ( \adultos\getStatus(ID) ) {
                case false: //No existe ?>
                    <article class="blockInfo red">
                        <header><h1>La cuenta no existe</h1></header>
                        <p>Ésto no debería de haber sucedido. Prueba a crear la cuenta de nuevo o ponte en contacto con nosotros</p>
                <?php break;
                case \adultos\estado::password: // Password ?>
                    <article class="blockInfo red">
                        <header><h1>Cambiar contraseña</h1></header>
                        <p>La contraseña de esta cuenta es inválida. Ponte en contacto con nosotros</p>
                <?php break;
                case \adultos\estado::activo: // Ya activada ?>
                    <article class="blockInfo red">
                        <header><h1>La cuenta ya está activada</h1></header>
                        <p>No es necesario que hagas nada más</p>
                <?php break;
                case \adultos\estado::email: // Email 
                    \adultos\setStatus(ID, \adultos\estado::activo,);
                ?>
                    <article class="clean green">
                        <header><h1><span class="FILLpink">⭐</span> Cuenta activada <span class="FILLpurple">⭐</span></h1></header>

                        <p>Cuenta activada.</p>
                        <p>Ya puedes acceder a tu perfil en el menú lateral o pinchando <a href="/perfil">aquí</a></p>
                <?php break;
            }
        ?>
        
        <?php } ?>
    
        </article>
    </main>

    <?php include(__DIR__.'/../footer.php');?>
</body>
</html>