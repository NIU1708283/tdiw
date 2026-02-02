<?php
// Model: Consulta Categories de la BD

function consultaCategories($connexio) {
    $query = "SELECT id, nom, descripcio, images FROM categoria ORDER BY id ASC";
    $result = pg_query($connexio, $query);
    
    if (!$result) {
        return [];
    }
    
    return pg_fetch_all($result) ?: [];
}

function obtenerCategoriaById($connexio, $id) {
    $query = "SELECT id, nom, descripcio, images FROM categoria WHERE id = $1";
    $result = pg_query_params($connexio, $query, array($id));
    
    if (!$result) {
        return null;
    }
    
    return pg_fetch_assoc($result);
}
?>
