<?php
declare(strict_types=1);
function consultaCategories($connexio) {
    $sql = "SELECT id, nom, descripcio, images FROM categoria ORDER BY id ASC";
    $consulta = pg_query($connexio, $sql);
    
    if (!$consulta) {
        throw new Exception("(pg_query) " . pg_last_error());
    }
    
    $resultat_categories = pg_fetch_all($consulta);
    return $resultat_categories;
}
?>
