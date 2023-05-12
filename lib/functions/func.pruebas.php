<?php
namespace pruebas {
    enum tipo {
        case aguante;
        case velocidad;
        case puntos;
        case race;
    }

    function find( int $id=null, tipo $tipo=null, string $nombre=null) {
        global $DB;
        $q1 = $q2 = $q3 = '';

        if ($id)        { $q1 = "AND id = $id"; }
        if ($tipo)      { $q2 = "AND tipo = '$tipo->name'"; }
        if ($nombre)    { $q3 = "AND nombre = '$nombre'"; }

        $sql = <<<CONSULTA
            SELECT * FROM pruebas WHERE 1 $q1 $q2 $q3
            CONSULTA;

        $dbData = $DB->consulta($sql);
        foreach ($dbData as $k => $v) {
            $dbData[$k]['data'] = \json_decode( $dbData[$k]['data'] );
        }

        return $dbData;
    }
    
    function listado(array $ids=null) {
        global $DB;

        $sql = <<<CONSULTA
        SELECT 
        id, tipo, nombre, info, data
        FROM pruebas
        CONSULTA;

        if ($ids) { $sql .= ' WHERE id IN ('.implode(',', $ids) . ')'; }
        
        $dbData = $DB->consulta($sql);
        $returnData = array();
        foreach ($dbData as $dbRow) { 
            $dbRow['data'] = \json_decode( $dbRow['data'] );
            $returnData[$dbRow['id']] = $dbRow; 
        }
        return $returnData;
    }
}

namespace obstaculos {
    function listado(array $ids=null) {
        global $DB;

        $sql = <<<CONSULTA
        SELECT 
        id, nombre, puntos
        FROM obstaculos
        CONSULTA;

        if ($ids) { $sql .= ' WHERE id IN ('.implode(',', $ids) . ')'; }
        
        $dbData = $DB->consulta($sql);
        $returnData = array();
        foreach ($dbData as $dbRow) { $returnData[$dbRow['id']] = $dbRow; }
        return $returnData;
    }  

    function find( int $id=null, string $nombre=null, int $puntos=null) {
        global $DB;
        
        $q = array_fill(0,2,'');
        if ($id)        { $q[0] = "AND id = $id"; }
        if ($nombre)    { $q[1] = "AND nombre LIKE '%$nombre%'"; }
        if ($puntos)    { $q[2] = "AND puntos = $puntos"; }
        $qs = implode(' ', $q);

        $sql = <<<CONSULTA
            SELECT * FROM obstaculos WHERE 1 $qs
            CONSULTA;

        return $DB->consulta($sql);
    }
}
?>