<?php
declare(strict_types=1);
// Función para obtener productos por categoría
function consultaProductesPerCategoria($connexio, $categoria) {
    $sql = "SELECT p.id, p.nom, p.descripcio, p.preu, p.imatge 
            FROM producte p
            INNER JOIN categoria c ON p.id_categoria = c.id
            WHERE c.nom = $1 AND p.actiu = true
            ORDER BY p.id";
    
    $consulta = pg_query_params($connexio, $sql, array($categoria));
    
    if (!$consulta) {
        throw new Exception("(pg_query) " . pg_last_error());
    }
    
    $resultat_productes = pg_fetch_all($consulta);
    return $resultat_productes;
}

// Función para buscar productos en una categoría
function cercaProductesEnCategoria($connexio, $categoria, $cerca) {
    $sql = "SELECT p.id, p.nom, p.descripcio, p.preu, p.imatge 
            FROM producte p
            INNER JOIN categoria c ON p.id_categoria = c.id
            WHERE c.nom = $1 AND p.actiu = true
            AND (p.nom ILIKE $2 OR p.descripcio ILIKE $2)
            ORDER BY p.id";
    
    $cerca_param = '%' . $cerca . '%';
    $consulta = pg_query_params($connexio, $sql, array($categoria, $cerca_param));
    
    if (!$consulta) {
        throw new Exception("(pg_query) " . pg_last_error());
    }
    
    $resultat_productes = pg_fetch_all($consulta);
    return $resultat_productes;
}

// Función para obtener un producto por ID
function consultaProductePerID($connexio, $id) {
    $sql = "SELECT id, nom, descripcio, preu, imatge FROM producte WHERE id = $1";
    $consulta = pg_query_params($connexio, $sql, array($id));
    
    if (!$consulta) {
        throw new Exception("(pg_query) " . pg_last_error());
    }
    
    $resultat = pg_fetch_assoc($consulta);
    return $resultat;
}
?>
