<?php
// Vista: Home

require __DIR__ . '/partials/header.php';

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/consultaCategories.php';

$connexio = getConnection();
$categories = consultaCategories($connexio);
closeConnection($connexio);
?>

<div class="hero-section">
    <h1>Benvingut a la nostra botiga!</h1>
    <p>Descobreix els millors productes</p>
</div>

<section class="categories-section">
    <h2>Categories</h2>
    <div class="categories-grid">
        <?php if ($categories): ?>
            <?php foreach ($categories as $categoria): ?>
                <div class="category-card" onclick="loadCategory(<?= htmlspecialchars($categoria['id']) ?>)">
                    <?php if ($categoria['images']): ?>
                        <img src="<?= htmlspecialchars($categoria['images']) ?>" alt="<?= htmlspecialchars($categoria['nom']) ?>">
                    <?php else: ?>
                        <div class="image-placeholder">📦</div>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($categoria['nom']) ?></h3>
                    <p><?= htmlspecialchars($categoria['descripcio'] ?? '') ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hi ha categories disponibles</p>
        <?php endif; ?>
    </div>
</section>

<div id="productosContent"></div>

<?php require __DIR__ . '/partials/footer.php'; ?>
