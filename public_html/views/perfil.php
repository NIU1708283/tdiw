<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Redirigir si no està loguejat
if (!isset($_SESSION['usuari'])) {
    header('Location: index.php?action=iniciarsesio');
    exit;
}

// 2. RECUPERAR DADES ACTUALITZADES DE LA BASE DE DADES
// Això és vital per veure l'adreça i població, ja que la sessió potser no les té guardades.
require_once __DIR__ . '/../models/connectaBD.php';

$usuariSessio = $_SESSION['usuari'];
$connexio = connectaBD();

// Consultem totes les dades fresques de l'usuari per ID
$sql = "SELECT * FROM usuari WHERE id = $1";
$consulta = pg_query_params($connexio, $sql, array($usuariSessio['id']));
$dadesUsuari = pg_fetch_assoc($consulta);

pg_close($connexio);

// Si la consulta funciona, usem les dades de la BD. Si no, les de la sessió.
$usuari = $dadesUsuari ? $dadesUsuari : $usuariSessio;

// Missatges de feedback
$missatge_exit = $_GET['missatge_exit'] ?? null;
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Meu Perfil - ToonTunes</title>
    <link rel="stylesheet" href="css/style.css?v=10.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js?v=10.0" defer></script>
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    
    <div>
        <hr>
    </div>
    
    <main>
        <section class="perfil-container">
            <h2 class="perfil-title">El Meu Perfil</h2>
            
            <?php if ($missatge_exit === 'ok'): ?>
                <p class="missatge-exit">Perfil actualitzat correctament!</p>
            <?php endif; ?>

            <form action="index.php?action=actualitzar-perfil" method="post" enctype="multipart/form-data" class="perfil-form">
                
                <fieldset class="perfil-section">
                    <legend>Dades Personals</legend>
                    
                    <div class="foto-perfil-preview">
                        <img src="<?= htmlspecialchars($usuari['foto_perfil'] ?? 'images/default.png') ?>" alt="Foto de perfil">
                    </div>
                    <label for="foto_perfil" style="text-align:center; margin-top:0;">Canviar foto:</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" class="perfil-input" accept="image/*" style="margin-bottom: 20px;">

                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" class="perfil-input" value="<?= htmlspecialchars($usuari['nom'] ?? '') ?>" required>
                    
                    <label for="email">Correu Electrònic:</label>
                    <input type="email" id="email" name="email" class="perfil-input" value="<?= htmlspecialchars($usuari['email'] ?? '') ?>" required>
                    
                    <label for="adreca">Adreça:</label>
                    <input type="text" id="adreca" name="adreca" class="perfil-input" value="<?= htmlspecialchars($usuari['adreca'] ?? '') ?>">
                    
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label for="poblacio">Població:</label>
                            <input type="text" id="poblacio" name="poblacio" class="perfil-input" value="<?= htmlspecialchars($usuari['poblacio'] ?? '') ?>">
                        </div>
                        <div style="width: 120px;">
                            <label for="codi_postal">Codi Postal:</label>
                            <input type="text" id="codi_postal" name="codi_postal" class="perfil-input" pattern="\d{5}" title="5 dígits" value="<?= htmlspecialchars($usuari['codi_postal'] ?? '') ?>">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="perfil-section" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <legend>Seguretat</legend>
                        <small>Deixa els camps en blanc si no vols canviar la contrasenya.</small>
                        
                        <label for="old_password">Contrasenya Actual:</label>
                        <input type="password" id="old_password" name="old_password" class="perfil-input" autocomplete="current-password">
                        
                        <label for="new_password">Nova Contrasenya:</label>
                        <input type="password" id="new_password" name="new_password" class="perfil-input" autocomplete="new-password">
                        
                        <label for="confirm_password">Confirmar:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="perfil-input" autocomplete="new-password">
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="perfil-submit-btn">Desar tots els canvis</button>
                    </div>
                </fieldset>

            </form>
        </section>
    </main>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>