<?php
namespace entradas {
    function listado($fields='*',$ids=false) {
        global $DB;
        $sql = "SELECT $fields FROM entradas";
        if ($ids) { $sql .= ' WHERE id IN ('.implode(',', $ids) . ')'; }

        $dbData = $DB->consulta($sql);
        $returnData = array();
        foreach ($dbData as $dbRow) { $returnData[$dbRow['id']] = $dbRow; }
        return $returnData;
    }
}
?>