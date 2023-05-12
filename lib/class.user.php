<?php

class user {
    public $id = 0;
    public $DNI = '';
    public $nombre = '';
    public $email = '';
    public $telefono = '';
    public $publi = false;

    public $estado = '';
    public $equipos = array();
    public $reservas = array();
    public $isLogged = false;

    
    function __construct() {
        if ( @$_SESSION['user']['id'] != '' ) {
            $this->fill_user_data( $_SESSION['user']['id'] );
            $this->isLogged = true;
        }
    }

    function fill_user_data(int $id) {
        global $DB;
        $sql = <<<CONSULTA
                SELECT * FROM adultos WHERE id=$id;
                SELECT id FROM equipos WHERE adulto=$id;
                CONSULTA;
        $dbAdulto = $DB->consulta($sql)[0];
        $this->id = $dbAdulto['id'];
        $this->nombre = $dbAdulto['nombre'];
        $this->DNI = $dbAdulto['DNI'];
        $this->email = $dbAdulto['email'];
        $this->telefono = $dbAdulto['telefono'];
        $this->publi = $dbAdulto['publi'];
        $this->estado = $dbAdulto['estado'];

        //Equipos
        $dbEquipos = $DB->nextRowset();
        foreach ($dbEquipos as $v) {
            $eq = new equipo($v['id']);
            array_push($this->equipos, $eq);
        }

        // Reservas
        $eqIds = implode(',', array_column($this->equipos, 'id') );
        if (!!$eqIds) {
            $sql = <<<CONSULTA
                SELECT r.id, r.fecha, r.pagado,
                eq.titulo AS eqTitulo, eq.nombre AS eqNombre,
                ev.nombre AS evNombre, ev.lugar AS evLugar,
                en.nombre AS enNombre, en.descripcion AS enDescripcion, en.precioWeb AS enPrecio,
                ev.fechaDesde AS evDesde, ev.fechaHasta AS evHasta
                FROM reservas AS r
                INNER JOIN equipos AS eq ON r.equipo = eq.id
                INNER JOIN eventos AS ev ON r.evento = ev.id
                INNER JOIN entradas AS en ON r.entrada = en.id
                WHERE equipo IN ($eqIds) AND ev.fechaHasta >= CURDATE()
                ORDER BY FECHA DESC;
                CONSULTA;
            $this->reservas = $DB->consulta($sql);
        }
    }

    function loginPass(string $login, string $pass, string $field='DNI') {
        global $DB;
        $result = $DB->consulta('SELECT id,pass,estado FROM adultos WHERE '.$field.'="' . $login . '"');
        
        if ( count($result)>0 ) {
            if ($result[0]['estado']=='password') { return \adultos\estado::password; }
            if ( password_verify($pass, $result[0]['pass']) ) {
                if ($result[0]['estado']=='email') { return \adultos\estado::email; }
                $_SESSION['user']['id'] = $result[0]['id'];
                self::__construct();
                return \adultos\estado::activo;
            }
        } else { return false; } 
    }

    function unlog() {
        unset($_SESSION['user']);
        return true;
    }

    function getEquipo(int $id) {
        foreach ($this->equipos as $v) {
            if ($v->id == $id) { return $v; }
        }
        return false;
    }

    function getReserva(int $id) {
        foreach ($this->reservas as $v) {
            if ($v['id'] == $id) { return $v; }
        }
        return false;
    }


    function getReservasSinPagar() {
        $sinPago = array();
        foreach ($this->reservas as $v) {
            if ($v['pagado'] == 'no') { array_push($sinPago, $v); }
        }
        return $sinPago;
    }
}

?>