<?php
declare(strict_types=1);
// models/consultaComandes.php

require_once __DIR__ . '/connectaBD.php';

/**
 * Recupera totes les comandes d'un usuari específic.
 */
function obtenir_comandes_usuari(int $idUsuari): array {
    $conn = connectaBD();
    
    // Seleccionem les comandes ordenades per la més recent
    $sql = "SELECT id, data, import_total FROM comanda WHERE usuari_id = $1 ORDER BY data DESC";
    $res = pg_query_params($conn, $sql, array($idUsuari));
    
    $comandes = [];
    if ($res) {
        $comandes = pg_fetch_all($res) ?: [];
    }
    
    pg_close($conn);
    return $comandes;
}