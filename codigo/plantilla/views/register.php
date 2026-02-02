<?php
// Vista: Registre d'usuari

require __DIR__ . '/partials/header.php';

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="form-container">
    <h1>Crear compte nou</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=registrar" class="form">
        <div class="form-group">
            <label for="nom">Nom completo *</label>
            <input type="text" id="nom" name="nom" required pattern="[a-zA-Z\s]+" title="Només lletres i espais">
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contrasenya *</label>
            <input type="password" id="password" name="password" required minlength="6">
        </div>
        
        <div class="form-group">
            <label for="adreca">Adreça</label>
            <input type="text" id="adreca" name="adreca" maxlength="255">
        </div>
        
        <div class="form-group">
            <label for="poblacio">Població</label>
            <input type="text" id="poblacio" name="poblacio" maxlength="100">
        </div>
        
        <div class="form-group">
            <label for="codi_postal">Codi Postal *</label>
            <input type="text" id="codi_postal" name="codi_postal" required pattern="\d{5}" title="5 dígits obligatoris">
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Registrar-se</button>
    </form>
    
    <p class="text-center">Ja tens compte? <a href="index.php?action=login">Inicia sessió aquí</a></p>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
