<?php
// Model: Actualitzar dades d'usuari

function actualitzarUsuari($connexio, $id, $nom, $email, $adreca, $poblacio, $codi_postal, $password = null, $foto_perfil = null) {
    $errors = [];
    
    // Validacions
    if (empty($nom) || !preg_match('/^[a-zA-Z\s]+$/', $nom)) {
        $errors[] = "Nom invàlid";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invàlid";
    }
    
    if (!preg_match('/^\d{5}$/', $codi_postal)) {
        $errors[] = "Codi postal invàlid";
    }
    
    if (!empty($errors)) {
        return ['ok' => false, 'errors' => $errors];
    }
    
    // Construir la consulta dinàmicament
    $updates = [];
    $params = [];
    $param_count = 1;
    
    $updates[] = "nom = \$$param_count";
    $params[] = $nom;
    $param_count++;
    
    $updates[] = "email = \$$param_count";
    $params[] = $email;
    $param_count++;
    
    $updates[] = "adreca = \$$param_count";
    $params[] = $adreca;
    $param_count++;
    
    $updates[] = "poblacio = \$$param_count";
    $params[] = $poblacio;
    $param_count++;
    
    $updates[] = "codi_postal = \$$param_count";
    $params[] = $codi_postal;
    $param_count++;
    
    if ($password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updates[] = "password = \$$param_count";
        $params[] = $hashedPassword;
        $param_count++;
    }
    
    if ($foto_perfil) {
        $updates[] = "foto_perfil = \$$param_count";
        $params[] = $foto_perfil;
        $param_count++;
    }
    
    $updates[] = "id = \$$param_count";
    $params[] = $id;
    
    $query = "UPDATE usuari SET " . implode(", ", $updates) . " WHERE id = $" . $param_count;
    
    $result = pg_query_params($connexio, $query, $params);
    
    if (!$result) {
        return ['ok' => false, 'errors' => ['Error al actualitzar usuari']];
    }
    
    return ['ok' => true];
}
?>
