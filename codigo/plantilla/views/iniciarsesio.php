<?php
// Vista: Inici de sessió

require __DIR__ . '/partials/header.php';

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<div class="form-container">
    <h1>Inicia sessió</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=iniciar_sessio" class="form">
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contrasenya *</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Inicia sessió</button>
    </form>
    
    <p class="text-center">No tens compte? <a href="index.php?action=registre">Registra't aquí</a></p>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
