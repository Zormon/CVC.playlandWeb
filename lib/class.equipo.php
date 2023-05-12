<?php

class equipo {
    public $id = false;
    public $titulo;
    public $desde;
    public $nacimiento;
    public $edad;
    public $adulto;
    public $bando;
    public $grupoEdad = null;
    private $qrToken;

    //TODO: Gestionar esto fuera del grupo?
    private $tipoEntrada = false;
    private $pagado = 'no';
    private $intentos = 0;

    //TODO: Añadir otro constructor con el row ya lleno
    function __construct($id) {
        global $DB;
        $row = @$DB->consulta('SELECT * FROM equipos WHERE id='.$id)[0];

        if ($row) {
            $this->id = $row['id'];
            $this->titulo = $row['titulo'];
            $this->nombre = $row['nombre'];
            $this->nacimiento = $row['nacimiento'];
            $this->adulto = $row['adulto'];
            $this->bando = \bandos\find(id:$row['bando'])[0];
            $this->qrToken = $row['qrToken'];

            $bdt = DateTime::createFromFormat('Y-m-d', $this->nacimiento);
            $this->edad = \system\edad( $bdt );

            $dbGpr = @$DB->consulta('SELECT * from grupos_edad WHERE desde <= '.$this->edad.' AND hasta >= '.$this->edad)[0];
            if ($dbGpr) {
                $this->grupoEdad['nombre'] = $dbGpr['nombre'];
                $this->grupoEdad['desde'] = $dbGpr['desde'];
                $this->grupoEdad['hasta'] = $dbGpr['hasta'];
            }

            //TODO: Gestionar mejor esto? quiza no deberia formar parte de la definición de un equipo
            if (EVENTO_ACTUAL) {
                $ent = @$DB->consulta('SELECT entrada, pagado FROM reservas WHERE evento='.P_EVENTO_ACTUAL.' AND equipo='.$this->id . " AND dia='".HOY."'")[0];
                if ($ent) {
                    $this->tipoEntrada = $ent['entrada'];
                    $this->pagado = $ent['pagado'];
                    $this->intentos = (int)$DB->consulta("SELECT intentos FROM entradas WHERE id=".$this->tipoEntrada)[0]['intentos'];
                }
            }
         }
    }


    function checkQr($token) { return $this->qrToken==$token; }
    function haPagado() { return $this->pagado != 'no'; }

    function intentosRestantes($prueba) {
        if ($this->intentos) {
            global $DB;
            $EA = EVENTO_ACTUAL;
            // Se excluye resultado '-1' porque son races activas, no cuentan como participación
            $sql = <<<CONSULTA
                    SELECT COUNT(*) num
                    FROM participaciones
                    WHERE resultado!=-1
                    AND evento='{$EA['id']}'
                    AND equipo={$this->id}
                    AND prueba=$prueba
                    CONSULTA;
            $participaciones = (int)$DB->consulta($sql)[0]['num'];
            return $this->intentos - $participaciones;
        } else {
            return 0;
        }
    }
}

?>