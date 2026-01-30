<?php
declare(strict_types=1);
// Registre i verificació d'usuaris
// Inclou validacions i hash de contrasenyes

require_once __DIR__ . '/connectaBD.php';

function registra_usuari($nom, $email, $password, $adreca = null, $poblacio = null, $codi_postal = null) {
    $errors = [];
    
    // Validació del nom: mínim 2 caràcters, màxim 100
    $nom = trim($nom);
    if (!is_string($nom) || strlen($nom) < 2 || strlen($nom) > 100) {
        $errors[] = 'Nom invàlid (mínim 2, màxim 100 caràcters).';
    }
    
    // Validació de l'email amb filter_var
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Correu electrònic invàlid.';
    }
    
    // Validació de la contrasenya
    if (!is_string($password) || strlen($password) < 6 || strlen($password) > 128) {
        $errors[] = 'La contrasenya ha de tenir entre 6 i 128 caràcters.';
    }

    // Validació de l'adreça (opcional però màxim 30 caràcters si es proporciona)
    if ($adreca !== null && strlen($adreca) > 30) {
        $errors[] = 'La adreça no pot superar 30 caràcters.';
    }

    // Validació de la població (opcional però màxim 30 caràcters si es proporciona)
    if ($poblacio !== null && strlen($poblacio) > 30) {
        $errors[] = 'La població no pot superar 30 caràcters.';
    }

    if (!empty($errors)) return ['ok' => false, 'errors' => $errors];

    try {
        $conn = connectaBD();
        // Comprovar si ja existeix l'email
        $check = pg_query_params($conn, 'SELECT id FROM usuari WHERE email = $1', array($email));
        if ($check && pg_num_rows($check) > 0) {
            return ['ok' => false, 'errors' => ['Aquest correu ja està registrat.']];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = 'INSERT INTO usuari (nom, email, password_hash, adreca, poblacio, codi_postal, foto_perfil) VALUES ($1,$2,$3,$4,$5,$6,$7) RETURNING id';
        $params = array($nom, $email, $hash, $adreca, $poblacio, $codi_postal, null);
        $res = pg_query_params($conn, $query, $params);
        if ($res && pg_num_rows($res) > 0) {
            $row = pg_fetch_assoc($res);
            return ['ok' => true, 'id' => (int)$row['id'], 'nom' => $nom, 'email' => $email];
        }

        return ['ok' => false, 'errors' => ['No s\'ha pogut crear l\'usuari.']];

    } catch (Exception $e) {
        return ['ok' => false, 'errors' => ['Error de connexió amb la base de dades.']];
    }
}

/**
 * Verifica les credencials d'un usuari existent
 * @param string $email Correu de l'usuari
 * @param string $password Contrasenya en clar
 * @return array Resultat: ['ok' => true, 'usuari' => [...]] o ['ok' => false, 'errors' => [...]]
 */
function verifica_usuari(string $email, string $password): array {
    $errors = [];
    
    // Validacions bàsiques
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Correu electrònic invàlid.';
    }
    if (!is_string($password) || strlen($password) < 6) {
        $errors[] = 'La contrasenya ha de tenir almenys 6 caràcters.';
    }
    
    if (!empty($errors)) {
        return ['ok' => false, 'errors' => $errors];
    }
    
    try {
        $conn = connectaBD();
        
        // Buscar usuari per email
        $res = pg_query_params($conn, 'SELECT id, nom, password_hash FROM usuari WHERE email = $1', array($email));
        if (!$res) {
            throw new Exception('Error de base de dades.');
        }
        
        if (pg_num_rows($res) === 0) {
            return ['ok' => false, 'errors' => ['Aquest correu no està registrat.']];
        }
        
        $usuari = pg_fetch_assoc($res);
        
        // Verificar contrasenya amb password_verify
        if (!password_verify($password, $usuari['password_hash'])) {
            return ['ok' => false, 'errors' => ['Contrasenya incorrecta.']];
        }
        
        // Èxit: retornar dades de l'usuari (sense la contrasenya)
        return [
            'ok' => true,
            'usuari' => [
                'id' => (int)$usuari['id'],
                'nom' => $usuari['nom'],
                'email' => $email,
                'foto_perfil' => $usuari['foto_perfil'] ?? null
            ]
        ];
        
    } catch (Exception $e) {
        return ['ok' => false, 'errors' => ['Error de connexió amb la base de dades.']];
    }
}
