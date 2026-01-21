<?php
declare(strict_types=1);
require_once __DIR__ . '/connectaBD.php';

/**
 * Actualitza les dades del usuari a la base de dades
 */
function actualitza_dades_usuari(
    int $id, 
    string $nom,
    ?string $email = null,
    ?string $adreca = null,
    ?string $poblacio = null,
    ?string $codiPostal = null,
    ?string $foto = null,
    ?string $passwordHash = null
): bool {
    $conn = connectaBD();
    
    try {
        // Construïm la consulta de forma segura
        $updates = ['nom = $1'];
        $params = [$nom];
        $paramCount = 2;
        
        if ($email !== null && $email !== '') {
            $updates[] = "email = \$$paramCount";
            $params[] = $email;
            $paramCount++;
        }
        
        if ($adreca !== null) {
            $updates[] = "adreca = \$$paramCount";
            $params[] = $adreca;
            $paramCount++;
        }
        
        if ($poblacio !== null) {
            $updates[] = "poblacio = \$$paramCount";
            $params[] = $poblacio;
            $paramCount++;
        }
        
        if ($codiPostal !== null) {
            $updates[] = "codi_postal = \$$paramCount";
            $params[] = $codiPostal;
            $paramCount++;
        }
        
        if ($foto !== null && $foto !== '') {
            $updates[] = "foto_perfil = \$$paramCount";
            $params[] = $foto;
            $paramCount++;
        }
        
        if ($passwordHash !== null && $passwordHash !== '') {
            $updates[] = "password_hash = \$$paramCount";
            $params[] = $passwordHash;
            $paramCount++;
        }
        
        // Afegim l'ID del usuari com al final
        $params[] = $id;
        $updateStr = implode(', ', $updates);
        
        $query = "UPDATE usuari SET $updateStr WHERE id = \$$paramCount";
        $res = pg_query_params($conn, $query, $params);
        
        pg_close($conn);
        return $res !== false;
        
    } catch (Exception $e) {
        error_log("Error actualitzant usuari: " . $e->getMessage());
        pg_close($conn);
        return false;
    }
}