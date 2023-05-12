<?php
require(ROOT_DIR.'/lib/class.equipo.php');
require(ROOT_DIR.'/lib/class.user.php');
$USER = new user();
switch ($command[1]) {
    /* **************************************************************
    ************************* ADD / EDIT *****************************
    ************************************************************** */
    case 'nuevo': case 'modificar':
        define('ADD', $command[1]=='nuevo');

        if (ADD && $USER->isLogged) { 
            \system\respond(0x0100, 'Ya estás registrado como ' . $USER->nombre . '. Debes salir del sistema si quieres registrar a otro adulto.' );
        }

        // Nombre
        if (@!!!$_POST['nombre'])                                   { \system\respond(0x0101); }
        if (strlen($_POST['nombre']) < 3)                           { \system\respond(0x0102); }
        if (strlen($_POST['nombre']) > 80)                          { \system\respond(0x0103); }
        if (preg_match('/[^a-zA-ZÑñ ]/', $_POST['nombre']))         { \system\respond(0x0104); }
        
        // DNI
        if (@!!!$_POST['DNI'])                                      { \system\respond(0x0105); }
        if (strlen($_POST['DNI']) < 3)                              { \system\respond(0x0106); }
        if (strlen($_POST['DNI']) > 20)                             { \system\respond(0x0107); }
        if (preg_match('/[^a-zA-Z0-9]/', $_POST['DNI']))            { \system\respond(0x0108); }
        
        // Email
        if (@!!!$_POST['email'])                                    { \system\respond(0x0109); }
        if (strlen($_POST['email']) > 80)                           { \system\respond(0x010A); }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))    { \system\respond(0x010B); }
        
        // Telefono
        if (@!!!$_POST['telefono'])                                 { \system\respond(0x010C); }
        if (!preg_match('/[+]?[0-9]{9,12}/', $_POST['telefono']))   { \system\respond(0x010D); }

        // password
        if ( ADD && @!!!$_POST['pass'] )                            { \system\respond(0x010E); }
        if ( ADD || !!$_POST['pass'] ) {
            if (strlen($_POST['pass']) < 6)                         { \system\respond(0x010F); }
            if (strlen($_POST['pass']) > 30)                        { \system\respond(0x0110); }
            if (@!!!$_POST['passConfirm'])                          { \system\respond(0x0111); }
            if ($_POST['pass'] != $_POST['passConfirm'])            { \system\respond(0x0112); }
        }

        // Aceptar
        if ( ADD && @!$_POST['acepto'])                             { \system\respond(0x0113); }

        if ( $_POST['DNI'] != $USER->DNI && \adultos\getDNI($_POST['DNI']) )                    { \system\respond(0x0114, 'DNI ya registrado'); }
        if ( $_POST['email'] != $USER->email && \adultos\getEmail($_POST['email']) )            { \system\respond(0x0115, 'Email ya registrado'); }
        if ( $_POST['telefono'] != $USER->telefono && \adultos\getTel($_POST['telefono']) )     { \system\respond(0x0116, 'Teléfono ya registrado'); }


        if (!ADD) { // Modificar perfil
            $_POST['id'] = $USER->id;
            \adultos\edit($_POST, true);
        } else { // Nuevo usuario
            $newId = \adultos\add($_POST, true);
            $url = bin2hex(openssl_random_pseudo_bytes(2)).':x:'.dechex($newId);
            $url = bin2hex( \system\cifrar( $url ) );
            $url = 'https://playlandgrancanaria.com/perfil/activar/' . $url;


            $mail = new \system\mailer(true);
            $mail->Subject = 'Activa tu cuenta';
            $mail->addAddress($_POST['email'], $_POST['nombre']);
            $mail->Body = <<<MAIL
                            Estas a un paso de poder participar en el mejor evento familiar de Gran Canaria.<br>
                            Para confirmar tu cuenta sigue el siguiente enlace:
                            <a href="$url">$url</a>
                            MAIL;

            try {
                $mail->send();
            } catch (Exception $e) {
                \system\respond(0x0117, "Error al enviar correo de confirmación: {$mail->ErrorInfo}");
            }
        }

        \system\respond(0x0000);
    break;

    /* **************************************************************
    *********************** ENTRAR / SALIR **************************
    ************************************************************** */
    case 'entrar':
        if ( $USER->isLogged ) { \system\respond(0x0000); }
        if ( @!!!$_POST['login'] || @!!!$_POST['pass'] ) { \system\respond(0x0123); }

        $field = strpos($_POST['login'], '@')? 'email' : 'DNI';

        switch ( $USER->loginPass( $_POST['login'], $_POST['pass'], $field) ) {
            case \adultos\estado::activo:
                \system\respond(0x0000);
            case \adultos\estado::email:
                \system\respond(0x0120, 'Cuenta no activada');
            case \adultos\estado::password:
                \system\respond(0x0121, 'Se requiere cambio de contraseña');
            default:
                \system\respond(0x0122, 'Credenciales inválidas');
        }
    break;
    
    case 'salir':
        if ( $USER->unlog() ) { \system\respond(0x0000); }
    break;
}

?>