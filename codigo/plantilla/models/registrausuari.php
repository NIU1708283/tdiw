<?php
// Model: Registre i verificació d'usuaris

function registra_usuari($connexio, $nom, $email, $password, $adreca, $poblacio, $codi_postal) {
    $errors = [];
    
    // Validacions
    if (empty($nom) || !preg_match('/^[a-zA-Z\s]+$/', $nom)) {
        $errors[] = "Nom invàlid (només lletres i espais)";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invàlid";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Contrasenya massa curta (mínim 6 caràcters)";
    }
    
    if (strlen($adreca) > 255) {
        $errors[] = "Adreça massa llarga";
    }
    
    if (strlen($poblacio) > 100) {
        $errors[] = "Població massa llarga";
    }
    
    if (!preg_match('/^\d{5}$/', $codi_postal)) {
        $errors[] = "Codi postal invàlid (5 dígits)";
    }
    
    if (!empty($errors)) {
        return ['ok' => false, 'errors' => $errors];
    }
    
    // Verificar si l'email ja existeix
    $query = "SELECT id FROM usuari WHERE email = $1";
    $result = pg_query_params($connexio, $query, array($email));
    if (pg_num_rows($result) > 0) {
        return ['ok' => false, 'errors' => ['Email ja registrat']];
    }
    
    // Hash de la contrasenya
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Inserir usuari - CONSULTA PARAMETRITZADA
    $query = "INSERT INTO usuari (nom, email, password, adreca, poblacio, codi_postal) 
              VALUES ($1, $2, $3, $4, $5, $6) RETURNING id";
    $result = pg_query_params($connexio, $query, array($nom, $email, $hashedPassword, $adreca, $poblacio, $codi_postal));
    
    if (!$result) {
        return ['ok' => false, 'errors' => ['Error al registrar usuari']];
    }
    
    $row = pg_fetch_assoc($result);
    
    return [
        'ok' => true,
        'id' => $row['id'],
        'nom' => $nom,
        'email' => $email
    ];
}

function verifica_usuari($connexio, $email, $password) {
    $errors = [];
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invàlid";
    }
    
    if (empty($password)) {
        $errors[] = "Contrasenya requerida";
    }
    
    if (!empty($errors)) {
        return ['ok' => false, 'errors' => $errors];
    }
    
    // Buscar usuari per email - CONSULTA PARAMETRITZADA
    $query = "SELECT id, nom, email, password FROM usuari WHERE email = $1";
    $result = pg_query_params($connexio, $query, array($email));
    
    if (pg_num_rows($result) == 0) {
        return ['ok' => false, 'errors' => ['Email no registrat']];
    }
    
    $user = pg_fetch_assoc($result);
    
    // Verificar contrasenya
    if (!password_verify($password, $user['password'])) {
        return ['ok' => false, 'errors' => ['Contrasenya incorrecta']];
    }
    
    return [
        'ok' => true,
        'id' => $user['id'],
        'nom' => $user['nom'],
        'email' => $user['email']
    ];
}

function obtenerUsuarioById($connexio, $id) {
    $query = "SELECT id, nom, email, adreca, poblacio, codi_postal, foto_perfil FROM usuari WHERE id = $1";
    $result = pg_query_params($connexio, $query, array($id));
    
    if (!$result || pg_num_rows($result) == 0) {
        return null;
    }
    
    return pg_fetch_assoc($result);
}
?>
