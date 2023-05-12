<?php
namespace adultos {
    enum estado {
        case activo;
        case email;
        case password;
    }


    function find(
            int $id=null, string $nombre=null,
            string $DNI=null, string $email=null,
            string $telefono=null, estado $estado=null) {
        global $DB;
        $q1 = $q2 = $q3 = $q4 = $q5 = $q6 = '';

        if ($id)        { $q1 = "AND id = $id"; }
        if ($nombre)    { $q2 = "AND nombre = '$nombre'"; }
        if ($DNI)       { $q3 = "AND DNI = '$DNI'"; }
        if ($email)     { $q4 = "AND email = '$email'"; }
        if ($telefono)  { $q5 = "AND telefono = '$telefono'"; }
        if ($estado)    { $q6 = "AND estado = '$estado->name'"; }

        $sql = <<<CONSULTA
            SELECT * FROM adultos WHERE 1 $q1 $q2 $q3 $q4 $q5 $q6
            CONSULTA;

        return $DB->consulta($sql);
    }

    function listado($ids=false) {
        global $DB;
        $sql = 'SELECT id, DNI, nombre, email, telefono, estado FROM adultos';
        if ($ids) { $sql .= ' WHERE id IN ('.implode(',', $ids) . ')'; }

        $dbData = $DB->consulta($sql);
        $returnData = array();
        foreach ($dbData as $dbRow) { $returnData[$dbRow['id']] = $dbRow; }
        return $returnData;
    }
    
    function getEquipos($dni) {
        global $DB;
        $id = @$DB->consulta('SELECT id FROM adultos WHERE DNI="'.$dni.'"')[0]['id'];

        $sql = 'SELECT id FROM equipos WHERE adulto='.$id;
        return array_column($DB->consulta($sql), 'id');
    }


    function getStatus($id) {
        global $DB;
        $result = $DB->consulta("SELECT estado FROM adultos WHERE id='" . $id . "'");

        if (!count($result)>0) { return false; }

        switch ($result[0]['estado']) {
            case 'activo': return estado::activo;
            case 'email': return estado::email;
            case 'password': return estado::password;
        }
    }
    
    function setStatus($id, $status) {
        global $DB;

        $stat = '';
        switch ( $status ) {
            case estado::activo:    $stat = 'activo';   break;
            case estado::password:  $stat = 'password'; break;
            case estado::email:     $stat = 'email';    break;
        }

        $DB->consulta("UPDATE adultos SET estado='".$stat."' WHERE id=".$id, false);
    }

    function getDNI($DNI) {
        global $DB;
        $result = $DB->consulta("SELECT DNI FROM adultos WHERE DNI='" . $DNI . "'");

        return count($result)>0? $result[0]['DNI'] : false;
    }

    function getEmail($email) {
        global $DB;
        $result = $DB->consulta("SELECT email FROM adultos WHERE email='" . $email . "'");

        return count($result)>0? $result[0]['email'] : false;
    }

    function getTel($tel) {
        global $DB;
        $result = $DB->consulta("SELECT telefono FROM adultos WHERE telefono='" . $tel . "'");

        return count($result)>0? $result[0]['telefono'] : false;
    }

    function add($data, $web=false) {
        global $DB;

        if ($web) {
            $status = 'email';
            $hash = password_hash($data['pass'], PASSWORD_DEFAULT);
            $publi = isset($data['publi']);
        } else { // En taquilla
            $status = 'password';
            $hash = null;
            $publi = true;
        }

        $sql = <<<CONSULTA
                INSERT INTO adultos(nombre, DNI, pass, email, telefono, publi, estado)
                VALUES(
                    '{$data['nombre']}',
                    '{$data['DNI']}',
                    '$hash',
                    '{$data['email']}',
                    '{$data['telefono']}',
                    '$publi',
                    '$status'
                    )
                CONSULTA;

        $DB->consulta($sql, false);
        return $DB->lastInsertId();
    }

    function edit($data, $web=false) {
        global $DB;
        $ESTADO = !$web? ", estado = '" . $data['estado'] . "'" : '';
        $PUBLI = isset($data['publi']) ? 1 : 0;
        if ( isset($data['pass']) ) {
            $hash = password_hash($data['pass'], PASSWORD_DEFAULT);
            $PASS = ", pass = '" . $hash . "'";
        } else { $PASS = ''; }

        

        $sql = <<<CONSULTA
        UPDATE adultos SET 
        nombre = '{$data['nombre']}',
        DNI = '{$data['DNI']}',
        telefono = '{$data['telefono']}',
        email = '{$data['email']}'$ESTADO$PASS,
        publi = $PUBLI
        WHERE id={$data['id']}
        CONSULTA;

        $DB->consulta($sql, false);
        return true;
    }

    function delete($id) {
        global $DB;

        $sql = <<<CONSULTA
                DROP TABLE IF EXISTS eqs;
                CREATE TEMPORARY TABLE eqs(id int);
                INSERT INTO eqs SELECT id FROM equipos WHERE adulto=$id;
                DELETE FROM reservas WHERE equipo IN (SELECT id FROM eqs);
                DELETE FROM participaciones WHERE equipo IN (SELECT id FROM eqs);
                DELETE FROM equipos WHERE adulto=$id;
                DELETE FROM adultos WHERE id=$id;
                CONSULTA;

        $DB->consulta($sql, false);
        return true;
    }
}
?>