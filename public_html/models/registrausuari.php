<?php
declare(strict_types=1);
// Funciones para registrar usuarios en la base de dades (Postgres)

require_once __DIR__ . '/connectaBD.php';

function registra_usuari($nom, $email, $password, $adreca = null, $poblacio = null, $codi_postal = null) {
    $errors = [];
    if (!is_string($nom) || trim($nom) === '') $errors[] = 'Nom invàlid.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correu electrònic invàlid.';
    if (!is_string($password) || strlen($password) < 6) $errors[] = 'La contrasenya ha de tenir almenys 6 caràcters.';

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
