<?php
declare(strict_types=1);
// models/consultaComandes.php
// Historial de comandes per usuari

require_once __DIR__ . '/connectaBD.php';

/**
 * Recupera totes les comandes d'un usuari específic.
 */
function obtenir_comandes_usuari(int $idUsuari): array {
    $conn = connectaBD();
    
    // Seleccionem les comandes ordenades per la més recent
    $sql = "SELECT id, data_comanda, preutotal as import_total, estat FROM comanda WHERE id_usuari = $1 ORDER BY data_comanda DESC";
    $res = pg_query_params($conn, $sql, array($idUsuari));
    
    $comandes = [];
    if ($res) {
        $comandes = pg_fetch_all($res) ?: [];
    }
    
    pg_close($conn);
    return $comandes;
}