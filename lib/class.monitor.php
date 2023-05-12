<?php

class monitor {
    public $isLogged = false;

    function __construct() {
        if ( @$_SESSION['monitor']['logged'] ) {
            $this->isLogged = true;
        }
    }

    function login($pass) {
        global $_CONFIG;
        if ( password_verify($pass, $_CONFIG['monitorPass']) ) {
            $_SESSION['monitor']['logged'] = true;
            self::__construct();
            return true;
        }
    }

    function unlog() {
        unset($_SESSION['monitor']);
        return true;
    }
}

?>