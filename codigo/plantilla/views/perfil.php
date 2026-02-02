<?php
// Vista: Perfil d'usuari

require __DIR__ . '/partials/header.php';

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/registrausuari.php';

if (!isset($_SESSION['usuari'])) {
    header('Location: index.php?action=login');
    exit;
}

$connexio = getConnection();
$usuari = obtenerUsuarioById($connexio, $_SESSION['usuari']['id']);
closeConnection($connexio);

$fotoSrc = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23ccc" width="200" height="200"/%3E%3Ctext fill="%23999" font-size="20" x="50" y="100"%3ESin foto%3C/text%3E%3C/svg%3E';

if ($usuari && $usuari['foto_perfil']) {
    $fotoSrc = 'uploadedFiles/' . htmlspecialchars($usuari['foto_perfil']);
}
?>

<div class="profile-container">
    <h1>El meu perfil</h1>
    
    <div class="profile-card">
        <div class="profile-header">
            <img src="<?= $fotoSrc ?>" alt="Foto de perfil" class="profile-photo">
            <div class="profile-info">
                <h2><?= htmlspecialchars($usuari['nom'] ?? '') ?></h2>
                <p><?= htmlspecialchars($usuari['email'] ?? '') ?></p>
            </div>
        </div>
        
        <div class="profile-details">
            <h3>Informació personal</h3>
            <p><strong>Adreça:</strong> <?= htmlspecialchars($usuari['adreca'] ?? 'No proporcionada') ?></p>
            <p><strong>Població:</strong> <?= htmlspecialchars($usuari['poblacio'] ?? 'No proporcionada') ?></p>
            <p><strong>Codi Postal:</strong> <?= htmlspecialchars($usuari['codi_postal'] ?? 'No proporcionat') ?></p>
        </div>
        
        <div class="profile-actions">
            <a href="index.php?action=editar_perfil" class="btn btn-primary">Editar perfil</a>
            <a href="index.php?action=historial" class="btn btn-secondary">Les meves comandes</a>
            <a href="index.php?action=logout" class="btn btn-danger">Tancar sessió</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
