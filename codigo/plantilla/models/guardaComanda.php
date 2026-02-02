<?php
// Model: Guardar comanda a la BD

function guardarComanda($connexio, $usuari_id, $items_carretó, $total) {
    // Iniciar transacció
    pg_query($connexio, "BEGIN");
    
    try {
        // Inserir comanda
        $query = "INSERT INTO comanda (usuari_id, total, estat) 
                  VALUES ($1, $2, 'pendent') RETURNING id";
        $result = pg_query_params($connexio, $query, array($usuari_id, $total));
        
        if (!$result) {
            throw new Exception("Error al inserir comanda");
        }
        
        $row = pg_fetch_assoc($result);
        $comanda_id = $row['id'];
        
        // Inserir línies de comanda
        foreach ($items_carretó as $item) {
            $query = "INSERT INTO liniacomanda (comanda_id, producte_id, quantitat, preu_unitari) 
                      VALUES ($1, $2, $3, $4)";
            $result = pg_query_params($connexio, $query, array(
                $comanda_id,
                $item['id'],
                $item['quantitat'],
                $item['preu']
            ));
            
            if (!$result) {
                throw new Exception("Error al inserir línies de comanda");
            }
        }
        
        // Confirmar transacció
        pg_query($connexio, "COMMIT");
        
        return ['ok' => true, 'comanda_id' => $comanda_id];
        
    } catch (Exception $e) {
        // Revertir transacció
        pg_query($connexio, "ROLLBACK");
        return ['ok' => false, 'error' => $e->getMessage()];
    }
}

function obtenerComanda($connexio, $comanda_id) {
    $query = "SELECT c.id, c.usuari_id, c.data_comanda, c.total, c.estat,
                     u.nom, u.email, u.adreca
              FROM comanda c
              JOIN usuari u ON c.usuari_id = u.id
              WHERE c.id = $1";
    
    $result = pg_query_params($connexio, $query, array($comanda_id));
    
    if (!$result || pg_num_rows($result) == 0) {
        return null;
    }
    
    return pg_fetch_assoc($result);
}

function obtenerLiniesComanda($connexio, $comanda_id) {
    $query = "SELECT lc.id, lc.producte_id, lc.quantitat, lc.preu_unitari,
                     p.nom, p.images
              FROM liniacomanda lc
              JOIN producte p ON lc.producte_id = p.id
              WHERE lc.comanda_id = $1";
    
    $result = pg_query_params($connexio, $query, array($comanda_id));
    
    if (!$result) {
        return [];
    }
    
    return pg_fetch_all($result) ?: [];
}

function obtenerComandesUsuari($connexio, $usuari_id) {
    $query = "SELECT id, data_comanda, total, estat
              FROM comanda
              WHERE usuari_id = $1
              ORDER BY data_comanda DESC";
    
    $result = pg_query_params($connexio, $query, array($usuari_id));
    
    if (!$result) {
        return [];
    }
    
    return pg_fetch_all($result) ?: [];
}
?>
