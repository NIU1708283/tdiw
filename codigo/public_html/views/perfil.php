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
    <link rel="stylesheet" href="style.css?v=10.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js?v=26.0" defer></script>
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
                <p class="missatge-exit" style="color: #4CAF50; background: #e8f5e9; padding: 12px; border-radius: 4px; margin-bottom: 20px;">✓ Perfil actualitzat correctament!</p>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <p class="missatge-error" style="color: #d32f2f; background: #ffebee; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                    ✗ Error: 
                    <?php 
                        $errors = [
                            'nom_invalid' => 'El nom és invàlid (entre 2 i 100 caràcters)',
                            'email_invalid' => 'Email invàlid',
                            'codi_postal_invalid' => 'Codi postal invàlid (5 dígits)',
                            'tipo_archivo_no_permitido' => 'Tipus de fitxer no permès (jpg, png, gif)',
                            'archivo_muy_grande' => 'Fitxer massa gran (màxim 5MB)',
                            'upload_failed' => 'Error pujant la foto',
                            'contrasenya_actual_requerida' => 'Contrasenya actual requerida',
                            'contrassenyes_no_coincideixen' => 'Les contrassenyes noves no coincideixen',
                            'contrasenya_length' => 'Contrasenya entre 6 i 128 caràcters',
                            'contrasenya_incorrecta' => 'Contrasenya actual incorrecta',
                            'bd_error' => 'Error actualitzant a la base de dades',
                        ];
                        $error = $_GET['error'];
                        echo htmlspecialchars($errors[$error] ?? 'Error desconegut');
                    ?>
                </p>
            <?php endif; ?>

            <!-- Formulari de visualització de dades -->
            <form class="perfil-form-display">
                
                <fieldset class="perfil-section">
                    <legend>Dades Personals</legend>
                    
                    <!-- Foto de perfil -->
                    <div class="foto-perfil-preview">
                        <?php
                            $fotoRuta = $usuari['foto_perfil'] ?? null;
                            if ($fotoRuta) {
                                $fotoSrc = 'uploadedFiles/' . htmlspecialchars($fotoRuta);
                            } else {
                                $fotoSrc = 'images/default.png';
                            }
                        ?>
                        <img src="<?= $fotoSrc ?>" alt="Foto de perfil">
                    </div>

                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" class="perfil-input" value="<?= htmlspecialchars($usuari['nom'] ?? '') ?>" disabled>
                    
                    <label for="email">Correu Electrònic:</label>
                    <input type="email" id="email" name="email" class="perfil-input" value="<?= htmlspecialchars($usuari['email'] ?? '') ?>" disabled>
                    
                    <label for="adreca">Adreça:</label>
                    <input type="text" id="adreca" name="adreca" class="perfil-input" value="<?= htmlspecialchars($usuari['adreca'] ?? '') ?>" disabled>
                    
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1;">
                            <label for="poblacio">Població:</label>
                            <input type="text" id="poblacio" name="poblacio" class="perfil-input" value="<?= htmlspecialchars($usuari['poblacio'] ?? '') ?>" disabled>
                        </div>
                        <div style="width: 120px;">
                            <label for="codi_postal">Codi Postal:</label>
                            <input type="text" id="codi_postal" name="codi_postal" class="perfil-input" value="<?= htmlspecialchars($usuari['codi_postal'] ?? '') ?>" disabled>
                        </div>
                    </div>
                </fieldset>

            </form>

            <!-- Botó per accedir a la pàgina de modificació -->
            <div style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
                <a href="index.php?action=editar-perfil" class="perfil-submit-btn" style="display: inline-block; text-decoration: none;">
                    Modificar dades
                </a>
            </div>
        </section>
    
    <?php require __DIR__ . '/partials/cart-sidebar.php'; ?>
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>