<?php
declare(strict_types=1);
// /models/guardaCabas.php

require_once __DIR__ . '/connectaBD.php';

/**
 * Guarda el cabàs d'un usuari a la base de dades
 */
function guardar_cabas_usuari(int $usuari_id, array $cart): bool {
    $connexio = connectaBD();
    
    try {
        // Començar transacció
        pg_query($connexio, "BEGIN");
        
        // Primer esborrem el cabàs anterior de l'usuari
        $sqlDelete = "DELETE FROM cabas WHERE usuari_id = $1";
        pg_query_params($connexio, $sqlDelete, array($usuari_id));
        
        // Insertar cada producte del cabàs
        foreach ($cart as $item) {
            $sqlInsert = "INSERT INTO cabas (usuari_id, producte_id, nom, preu, imatge, quantitat) 
                          VALUES ($1, $2, $3, $4, $5, $6)
                          ON CONFLICT (usuari_id, producte_id) 
                          DO UPDATE SET quantitat = EXCLUDED.quantitat, data_afegit = CURRENT_TIMESTAMP";
            
            $result = pg_query_params($connexio, $sqlInsert, array(
                $usuari_id,
                $item['id'],
                $item['nom'],
                $item['preu'],
                $item['imatge'] ?? '',
                $item['quantitat']
            ));
            
            if (!$result) {
                pg_query($connexio, "ROLLBACK");
                pg_close($connexio);
                return false;
            }
        }
        
        // Confirmar transacció
        pg_query($connexio, "COMMIT");
        pg_close($connexio);
        return true;
        
    } catch (Exception $e) {
        pg_query($connexio, "ROLLBACK");
        pg_close($connexio);
        return false;
    }
}

/**
 * Carrega el cabàs d'un usuari des de la base de dades
 */
function carregar_cabas_usuari(int $usuari_id): array {
    $connexio = connectaBD();
    
    $sql = "SELECT producte_id as id, nom, preu, imatge, quantitat 
            FROM cabas 
            WHERE usuari_id = $1 
            ORDER BY data_afegit DESC";
    
    $resultat = pg_query_params($connexio, $sql, array($usuari_id));
    
    $cart = [];
    if ($resultat) {
        while ($row = pg_fetch_assoc($resultat)) {
            $cart[] = [
                'id' => (int)$row['id'],
                'nom' => $row['nom'],
                'preu' => (float)$row['preu'],
                'imatge' => $row['imatge'],
                'quantitat' => (int)$row['quantitat']
            ];
        }
    }
    
    pg_close($connexio);
    return $cart;
}

/**
 * Esborra el cabàs d'un usuari de la base de dades
 */
function esborrar_cabas_usuari(int $usuari_id): bool {
    $connexio = connectaBD();
    
    $sql = "DELETE FROM cabas WHERE usuari_id = $1";
    $resultat = pg_query_params($connexio, $sql, array($usuari_id));
    
    $success = $resultat !== false;
    pg_close($connexio);
    
    return $success;
}
