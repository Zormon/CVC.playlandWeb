<?php

require(ROOT_DIR.'/lib/class.admin.php');
$ADMIN = new admin();

switch ( $command[1] ) {
    case 'login':
        if ( $ADMIN->isLogged )                                      { \system\respond(0x0500); }
        if (@!!!$_POST['user'])                                      { \system\respond(0x0501); }
        if (@!!!$_POST['pass'])                                      { \system\respond(0x0502); }

        if ($ADMIN->loginPass( $_POST['user'], $_POST['pass'] ))    { \system\respond(0x0000); }
        else                                                        { \system\respond(0x0503); }
    case 'logout':
        if ($ADMIN->unlog())                                        { \system\respond(0x0000); };
    break;
}

?>