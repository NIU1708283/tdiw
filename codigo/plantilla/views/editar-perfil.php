<?php
// Vista: Editar perfil

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

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
unset($_SESSION['errors']);
unset($_SESSION['success']);

$fotoSrc = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23ccc" width="200" height="200"/%3E%3Ctext fill="%23999" font-size="20" x="50" y="100"%3ESin foto%3C/text%3E%3C/svg%3E';

if ($usuari && $usuari['foto_perfil']) {
    $fotoSrc = 'uploadedFiles/' . htmlspecialchars($usuari['foto_perfil']);
}
?>

<div class="form-container">
    <h1>Editar perfil</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=actualitzar_perfil" enctype="multipart/form-data" class="form">
        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($usuari['nom'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($usuari['email'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="adreca">Adreça</label>
            <input type="text" id="adreca" name="adreca" value="<?= htmlspecialchars($usuari['adreca'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="poblacio">Població</label>
            <input type="text" id="poblacio" name="poblacio" value="<?= htmlspecialchars($usuari['poblacio'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="codi_postal">Codi Postal *</label>
            <input type="text" id="codi_postal" name="codi_postal" pattern="\d{5}" required value="<?= htmlspecialchars($usuari['codi_postal'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label for="foto_perfil">Foto de perfil (JPG, PNG, GIF - Màx 5MB)</label>
            <div class="foto-preview">
                <img id="fotoPreview" src="<?= $fotoSrc ?>" alt="Foto de perfil" style="max-width: 150px; border-radius: 50%;">
            </div>
            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" onchange="previewFoto(this)">
        </div>
        
        <div class="form-group">
            <label for="password">Nova contrasenya (deixar en blanc per mantenir la mateixa)</label>
            <input type="password" id="password" name="password" minlength="6">
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Guardar canvis</button>
    </form>
</div>

<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('fotoPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
