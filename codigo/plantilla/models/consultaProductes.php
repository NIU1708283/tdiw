<?php
// Model: Consulta Productes de la BD

function consultaProductes($connexio) {
    $query = "SELECT id, nom, descripcio, preu, images, categoria_id 
              FROM producte WHERE actiu = true ORDER BY id ASC";
    $result = pg_query($connexio, $query);
    
    if (!$result) {
        return [];
    }
    
    return pg_fetch_all($result) ?: [];
}

function consultaProductesPerCategoria($connexio, $categoria_id) {
    $query = "SELECT id, nom, descripcio, preu, images, categoria_id 
              FROM producte WHERE categoria_id = $1 AND actiu = true ORDER BY nom ASC";
    $result = pg_query_params($connexio, $query, array($categoria_id));
    
    if (!$result) {
        return [];
    }
    
    return pg_fetch_all($result) ?: [];
}

function cercaProductesEnCategoria($connexio, $categoria_id, $query_text) {
    $search = '%' . strtolower($query_text) . '%';
    $query = "SELECT id, nom, descripcio, preu, images, categoria_id 
              FROM producte 
              WHERE categoria_id = $1 AND actiu = true 
              AND (LOWER(nom) LIKE $2 OR LOWER(descripcio) LIKE $2)
              ORDER BY nom ASC";
    $result = pg_query_params($connexio, $query, array($categoria_id, $search));
    
    if (!$result) {
        return [];
    }
    
    return pg_fetch_all($result) ?: [];
}

function cercaProductesGlobal($connexio, $query_text) {
    $search = '%' . strtolower($query_text) . '%';
    $query = "SELECT id, nom, descripcio, preu, images, categoria_id 
              FROM producte 
              WHERE actiu = true 
              AND (LOWER(nom) LIKE $1 OR LOWER(descripcio) LIKE $1)
              ORDER BY nom ASC LIMIT 50";
    $result = pg_query_params($connexio, $query, array($search));
    
    if (!$result) {
        return [];
    }
    
    return pg_fetch_all($result) ?: [];
}

function obtenerProductoById($connexio, $id) {
    $query = "SELECT id, nom, descripcio, preu, images, categoria_id 
              FROM producte WHERE id = $1 AND actiu = true";
    $result = pg_query_params($connexio, $query, array($id));
    
    if (!$result) {
        return null;
    }
    
    return pg_fetch_assoc($result);
}
?>
