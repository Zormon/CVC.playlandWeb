<?php
namespace equipos {
    function find($id=null,$nombre=null,$titulo=null,$adulto=null) {
        global $DB;
        $q1 = $q2 = $q3 = $q4 = '';

        if ($id)     { $q1 = "AND id = $id"; }
        if ($titulo) { $q2 = "AND titulo = '$titulo'"; }
        if ($nombre) { $q3 = "AND nombre = '$nombre'"; }
        if ($adulto) { $q4 = "AND adulto = '$adulto'"; }

        $sql = <<<CONSULTA
            SELECT * FROM equipos WHERE 1 $q1 $q2 $q3 $q4
            CONSULTA;

        return $DB->consulta($sql);
    }

    function listado($ids=false) {
        global $DB;

        $sql = <<<CONSULTA
            SELECT 
            equipos.id id,
            equipos.titulo titulo,
            equipos.nombre nombre,
            equipos.bando bando,
            bandos.color color,
            DATE_FORMAT(equipos.nacimiento, '%d-%m-%Y') nacimiento,
            TIMESTAMPDIFF(YEAR,equipos.nacimiento,CURDATE()) edad,
            adultos.nombre adulto,
            adultos.id adultoId
            FROM equipos
            INNER JOIN adultos ON equipos.adulto = adultos.id
            INNER JOIN bandos ON equipos.bando = bandos.id
            CONSULTA;

        if ($ids) { $sql .= ' WHERE equipos.id IN ('.implode(',', $ids) . ')'; }
        
        $dbData = $DB->consulta($sql);
        $returnData = array();
        require(\ROOT_DIR.'/lib/class.equipo.php');
        foreach ($dbData as $dbRow) { 
            $returnData[$dbRow['id']] = $dbRow;
            $eq = new \equipo($dbRow['id']);
            if ($eq->haPagado()) { $returnData[$dbRow['id']]['pagado']=true; }
         }
        return $returnData;
    }

    /**
     * Edita un equipo en la base de datos
     *
     * @param array $data Array asociativo con los nuevos datos del equipo
     * ['titulo', 'nombre', 'nacimimento']
     * @param int $id Id del equipo a editar
     * @return bool True si la consulta fue exitosa
     */
    function edit($data) {
        global $DB;

        $sql = <<<CONSULTA
        UPDATE equipos SET 
        titulo = '{$data['titulo']}',
        nombre = '{$data['nombre']}',
        nacimiento = '{$data['nacimiento']}',
        bando = {$data['bando']}
        WHERE id = '{$data['id']}'
        CONSULTA;

        $DB->consulta($sql, false);

        return true;
    }

    function add($data, $adulto) {
        global $DB;
        $qrToken = bin2hex(random_bytes(2));

        $sql = <<<CONSULTA
                INSERT INTO equipos(titulo, nombre, nacimiento, qrToken, bando, adulto)
                VALUES(
                    '{$data['titulo']}',
                    '{$data['nombre']}',
                    '{$data['nacimiento']}',
                    '$qrToken',
                    {$data['bando']},
                    $adulto
                    )
                CONSULTA;
        try {
            $DB->consulta($sql, false);
            $eqID = $DB->lastInsertId();
        } catch(\PDOException $e) { return (int)$e->errorInfo[1]; }


        // QR de equipo
        require_once(ROOT_DIR . '/lib/qrcode.php');
        $qrData = 'e:'.dechex($eqID).','.$qrToken;
        $generator = new \QRCode($qrData, ['sf'=>1,'wq'=>0]);
        $qrImg = $generator->render_image();
        imagewebp( $qrImg, \ROOT_DIR.'/img/qrEquipos/'.$eqID.'.webp', 90 );

        return $eqID;
    }

    function delete($eqId) {
        global $DB;

        $sql = <<<CONSULTA
                DELETE FROM reservas WHERE equipo=$eqId;
                DELETE FROM participaciones WHERE equipo=$eqId;
                DELETE FROM equipos WHERE id=$eqId;
                CONSULTA;

        $DB->consulta($sql, false);
        return true;
    }

    function qrCodeB64($eqId) {
        $imgPath = \ROOT_DIR.'/img/qrEquipos/'.$eqId.'.webp';
        if ( \file_exists($imgPath) ) {
            return base64_encode( \file_get_contents($imgPath) );
        } else { return false; }
    }
}
?>