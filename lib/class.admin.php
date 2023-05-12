<?php

class admin {
    public $id = 0;
    public $login = '';
    public $permissions = array();
    public $isLogged = false;

    function __construct() {
        if ( @$_SESSION['admin']['id'] != '' ) {
            $this->fill_user_data( $_SESSION['admin']['id'] );
            $this->isLogged = true;
        }
    }

    function fill_user_data($id) {
        global $DB;
        $sql = 'SELECT * FROM admins WHERE id='.$id;
        $row = $DB->consulta($sql)[0];
        $this->id = $row['id'];
        $this->login = $row['login'];
    }

    function loginPass($login, $pass) {
        global $DB;
        $result = $DB->consulta('SELECT id, pass FROM admins WHERE login="' . $login . '"');

        if ( count($result)>0 && password_verify($pass, $result[0]['pass']) ) {
            $_SESSION['admin']['id'] = $result[0]['id'];
            self::__construct();
            return true;
        } else { return false; } 
    }

    function unlog() {
        unset($_SESSION['admin']);
        return true;
    }
    
    static function exists($login) {
        global $DB;
        $result = $DB->consulta("SELECT id FROM admins WHERE login=" . $login);

        return count($result)>0;
    }

}

?>