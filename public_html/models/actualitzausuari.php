<?php
require_once __DIR__ . '/connectaBD.php';

function actualitza_dades_usuari($id, $nom, $foto = null) {
    $conn = connectaBD();
    
    if ($foto) {
        $query = "UPDATE usuari SET nom = $1, foto_perfil = $2 WHERE id = $3";
        $res = pg_query_params($conn, $query, array($nom, $foto, $id));
    } else {
        $query = "UPDATE usuari SET nom = $1 WHERE id = $2";
        $res = pg_query_params($conn, $query, array($nom, $id));
    }
    
    return $res !== false;
}