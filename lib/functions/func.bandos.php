<?php
namespace bandos {
    function listado($ids=false) {
        global $DB;
        $sql = 'SELECT * FROM bandos';
        if ($ids) { $sql .= ' WHERE id IN ('.implode(',', $ids) . ')'; }

        $dbData = $DB->consulta($sql);
        $returnData = array();
        foreach ($dbData as $dbRow) { $returnData[$dbRow['id']] = $dbRow; }
        return $returnData;
    }

    function find($fields='*',int $id=null, string $nombre=null, string $color=null) {
        global $DB;
        
        $q = array_fill(0,2,'');
        if ($id)        { $q[0] = "AND id = $id"; }
        if ($nombre)    { $q[1] = "AND nombre = '$nombre'"; }
        if ($color)     { $q[2] = "AND color = '$color'"; }
        $qs = implode(' ', $q);

        $sql = <<<CONSULTA
            SELECT $fields FROM bandos WHERE 1 $qs
            CONSULTA;

        return $DB->consulta($sql);
    }
}
?>