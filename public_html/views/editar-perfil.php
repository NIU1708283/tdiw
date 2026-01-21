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
    <title>Modificar El Meu Perfil - ToonTunes</title>
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
            <h2 class="perfil-title">Modificar Les Meves Dades</h2>
            
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

            <!-- Formulari de dades -->
            <form action="index.php?action=actualitzar-perfil" method="post" enctype="multipart/form-data" class="perfil-form">
                
                <fieldset class="perfil-section">
                    <legend>Dades Personals</legend>
                    
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
                    
                    <!-- Selector de fitxer personalitzat -->
                    <div style="text-align:center; margin: 20px 0;">
                        <label for="foto_perfil" class="custom-file-upload">
                            Seleccionar nova foto
                        </label>
                        <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" style="display: none;" onchange="updateFileName(this)">
                        <div id="file-name" style="margin-top: 8px; font-size: 13px; color: var(--muted);"></div>
                    </div>

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

                <fieldset class="perfil-section">
                    <legend>Seguretat</legend>
                    <small>Deixa els camps en blanc si no vols canviar la contrasenya.</small>
                    
                    <label for="old_password">Contrasenya Actual:</label>
                    <input type="password" id="old_password" name="old_password" class="perfil-input" autocomplete="current-password">
                    
                    <label for="new_password">Nova Contrasenya:</label>
                    <input type="password" id="new_password" name="new_password" class="perfil-input" autocomplete="new-password">
                    
                    <label for="confirm_password">Confirmar:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="perfil-input" autocomplete="new-password">
                </fieldset>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="perfil-submit-btn" style="flex: 1;">Guardar Canvis</button>
                    <a href="index.php?action=perfil" class="perfil-cancel-btn" style="flex: 1; display: flex; align-items: center; justify-content: center; text-decoration: none;">Tornar Enrere</a>
                </div>

            </form>
        </section>
    </main>
    
    <script>
    function updateFileName(input) {
        const fileNameDiv = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            fileNameDiv.textContent = '✓ Fitxer seleccionat: ' + input.files[0].name;
            fileNameDiv.style.color = 'var(--brand)';
        } else {
            fileNameDiv.textContent = '';
        }
    }
    </script>
    
    <style>
    .custom-file-upload {
        display: inline-block;
        padding: 12px 24px;
        cursor: pointer;
        background: var(--brand);
        color: white;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 2px solid var(--brand);
    }
    
    .custom-file-upload:hover {
        background: transparent;
        color: var(--brand);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 69, 0, 0.2);
    }
    
    /* Millorar simetria del formulari */
    .perfil-form {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .perfil-section {
        margin-bottom: 25px;
        min-height: auto;
    }
    
    .perfil-section small {
        display: block;
        margin-bottom: 15px;
        color: var(--muted);
    }
    
    .perfil-input {
        width: 100%;
    }
    
    .perfil-submit-btn {
        width: 100%;
    }

    .perfil-cancel-btn {
        padding: 12px 24px;
        cursor: pointer;
        background: transparent;
        color: var(--brand);
        border: 2px solid var(--brand);
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .perfil-cancel-btn:hover {
        background: var(--brand);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 69, 0, 0.2);
    }
    </style>
    
    <?php require __DIR__ . '/partials/cart-sidebar.php'; ?>
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
