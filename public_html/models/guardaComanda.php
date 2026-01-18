<?php
declare(strict_types=1);
// models/guardaComanda.php

require_once __DIR__ . '/connectaBD.php';

/**
 * Desa una comanda i les seves línies a la base de dades.
 * Utilitza consultes preparades per evitar SQL Injection.
 */
function guardar_comanda(int $idUsuari, float $total, array $productes): bool {
    $conn = connectaBD();
    
    try {
        // Iniciem una transacció per assegurar que es guarden les línies si la comanda es crea
        pg_query($conn, "BEGIN");

        // 1. Inserció a la taula 'comanda'
        // Segons el model de la Sessió 6, cal desar l'ID de l'usuari, la data actual i el total
        $queryComanda = "INSERT INTO comanda (usuari_id, data, import_total) 
                         VALUES ($1, CURRENT_TIMESTAMP, $2) 
                         RETURNING id";
        
        $resComanda = pg_query_params($conn, $queryComanda, array($idUsuari, $total));
        
        if (!$resComanda || pg_num_rows($resComanda) === 0) {
            throw new Exception("Error al crear la comanda principal.");
        }
        
        $idComanda = (int) pg_fetch_result($resComanda, 0, 0);

        // 2. Inserció a la taula 'linia_comanda' per a cada producte del cabàs
        // Cada línia vincula la comanda amb el producte, la quantitat i el preu en aquell moment
        $queryLinia = "INSERT INTO linia_comanda (comanda_id, producte_id, quantitat, preu_unitari) 
                       VALUES ($1, $2, $3, $4)";
        
        // Preparem la consulta una sola vegada per eficiència
        pg_prepare($conn, "insert_linia", $queryLinia);

        foreach ($productes as $item) {
            $resLinia = pg_execute($conn, "insert_linia", array(
                $idComanda, 
                $item['id'], 
                $item['quantitat'], 
                $item['preu']
            ));
            
            if (!$resLinia) {
                throw new Exception("Error al crear una línia de la comanda.");
            }
        }

        // Si tot ha anat bé, confirmem els canvis
        pg_query($conn, "COMMIT");
        return true;

    } catch (Exception $e) {
        // Si hi ha qualsevol error, desfem els canvis a la BD
        pg_query($conn, "ROLLBACK");
        error_log($e->getMessage());
        return false;
    } finally {
        pg_close($conn);
    }
}