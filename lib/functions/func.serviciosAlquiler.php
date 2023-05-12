<?php
namespace serviciosAlquiler {
    function find($indexById=false,$id=null,$nombre=null,$video=null,$nombreURL=null) {
        global $DB;
        
        $q = array_fill(0,3,'');
        if ($id)        { $q[0] = "AND id = $id"; }
        if ($nombre)    { $q[1] = "AND nombre LIKE '%$nombre%'"; }
        if ($video)     { $q[2] = "AND video LIKE '%$video%'"; }
        if ($nombreURL) { $q[3] = "AND nombreURL = '$nombreURL'"; }
        $qs = implode(' ', $q);

        $sql = <<<CONSULTA
            SELECT *, UNIX_TIMESTAMP(version) version FROM servicios_alquiler WHERE 1 $qs
            CONSULTA;

        $dbData = $DB->consulta($sql);
        $returnData = array();
        if ($indexById)     { foreach ($dbData as $dbRow) { $returnData[$dbRow['id']] = $dbRow; } }
        else                { $returnData = $dbData; }

        return $returnData;
    }
}
?>