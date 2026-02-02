<?php
declare(strict_types=1);
// models/guardaComanda.php
// Guarda les comandes a la BD amb transaccions

require_once __DIR__ . '/connectaBD.php';

/**
 * Desa una comanda i les seves línies a la base de dades.
 * Utilitza consultes preparades per evitar SQL Injection.
 * 
 * @return int|false L'ID de la comanda si s'ha guardat correctament, false si ha fallat
 */
function guardar_comanda(int $idUsuari, float $total, array $productes) {
    $conn = connectaBD();
    
    try {
        // Iniciem una transacció per assegurar que es guarden les línies si la comanda es crea
        pg_query($conn, "BEGIN");

        // 1. Inserció a la taula 'comanda'
        // Segons el model de la Sessió 6, cal desar l'ID de l'usuari, la data actual i el total
        $queryComanda = "INSERT INTO comanda (id_usuari, data_comanda, preutotal, estat) 
                         VALUES ($1, CURRENT_TIMESTAMP, $2, 'Pendent') 
                         RETURNING id";
        
        $resComanda = pg_query_params($conn, $queryComanda, array($idUsuari, $total));
        
        if (!$resComanda || pg_num_rows($resComanda) === 0) {
            throw new Exception("Error al crear la comanda principal.");
        }
        
        $idComanda = (int) pg_fetch_result($resComanda, 0, 0);

        // 2. Inserció a la taula 'liniacomanda' per a cada producte del cabàs
        // Cada línia vincula la comanda amb el producte i la quantitat
        $queryLinia = "INSERT INTO liniacomanda (id_comanda, id_producte, unitats) 
                       VALUES ($1, $2, $3)";
        
        // Preparem la consulta una sola vegada per eficiència
        pg_prepare($conn, "insert_linia", $queryLinia);

        foreach ($productes as $item) {
            $resLinia = pg_execute($conn, "insert_linia", array(
                $idComanda, 
                $item['id'], 
                $item['quantitat']
            ));
            
            if (!$resLinia) {
                throw new Exception("Error al crear una línia de la comanda.");
            }
        }

        // Si tot ha anat bé, confirmem els canvis
        pg_query($conn, "COMMIT");
        return $idComanda;

    } catch (Exception $e) {
        // Si hi ha qualsevol error, desfem els canvis a la BD
        pg_query($conn, "ROLLBACK");
        error_log($e->getMessage());
        return false;
    } finally {
        pg_close($conn);
    }
}