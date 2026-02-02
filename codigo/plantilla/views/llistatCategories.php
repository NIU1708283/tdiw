<?php
// Vista: Llistat de categories i productes

require __DIR__ . '/partials/header.php';

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/consultaCategories.php';

$connexio = getConnection();
$categories = consultaCategories($connexio);
closeConnection($connexio);
?>

<div class="categories-section">
    <h2>Totes les categories</h2>
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

<!-- PRODUCTES (carregats per AJAX) -->
<section id="productosSection" class="products-section" style="display:none;">
    <div class="search-in-category">
        <input type="text" id="categorySearch" placeholder="Buscar en aquesta categoria..." onkeyup="searchCategoryProducts()">
    </div>
    <div id="productosContainer" class="products-grid">
        <!-- Omplert per AJAX -->
    </div>
</section>

<!-- DETALL DE PRODUCTE (modal AJAX) -->
<div id="productDetailModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeProductDetail()">&times;</span>
        <div id="productDetailContent">
            <!-- Omplert per AJAX -->
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
