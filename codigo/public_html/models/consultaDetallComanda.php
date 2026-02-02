<?php
declare(strict_types=1);
// models/consultaDetallComanda.php
// Detall complet d'una comanda amb les seves línies

require_once __DIR__ . '/connectaBD.php';

/**
 * Obtén els detalls d'una comanda específica amb les seves línies
 */
function obtenir_detall_comanda(int $idComanda): ?array {
    $conn = connectaBD();
    
    try {
        // Obtenim els detalls de la comanda
        $sqlComanda = "SELECT id, id_usuari, data_comanda, preutotal as preuTotal, estat FROM comanda WHERE id = $1";
        $resComanda = pg_query_params($conn, $sqlComanda, array($idComanda));
        
        if (!$resComanda || pg_num_rows($resComanda) === 0) {
            return null;
        }
        
        $comanda = pg_fetch_assoc($resComanda);
        
        // Obtenim les línies de la comanda amb els detalls dels productes
        $sqlLinies = "SELECT 
                        lc.id,
                        lc.id_producte,
                        lc.unitats,
                        p.nom,
                        p.preu,
                        p.imatge,
                        (p.preu * lc.unitats) as subtotal
                      FROM liniacomanda lc
                      JOIN producte p ON lc.id_producte = p.id
                      WHERE lc.id_comanda = $1
                      ORDER BY lc.id";
        
        $resLinies = pg_query_params($conn, $sqlLinies, array($idComanda));
        $comanda['linies'] = pg_fetch_all($resLinies) ?: [];
        
        return $comanda;
        
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    } finally {
        pg_close($conn);
    }
}
?>