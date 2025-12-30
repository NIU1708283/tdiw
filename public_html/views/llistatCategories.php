<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Botiga - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=23.0">
    
    <script src="script.js?v=23.0" defer></script>
</head>

<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    
    <div>
        <hr>
    </div>
    
    <main>
        <section id="categories-section">
            <h1 style="text-align: center; margin: 2rem 0; color: #ff4500;">Botiga - Selecciona una Categoria</h1>
            
            <div class="categories-grid">
                <?php 
                if ($resultat_categories && count($resultat_categories) > 0) {
                    // Ordenar de la Z a la A
                    usort($resultat_categories, function($a, $b) {
                        return strcmp($b['nom'], $a['nom']);
                    });
                    
                    foreach($resultat_categories as $categoria) {
                        $nomDB = $categoria['nom'];
                        $imatge = $categoria['images'] ?: '';
                        $descripcio = $categoria['descripcio'] ?: '';
                        
                        // Tarjeta clickeable con AJAX
                        echo '<div class="category-card" onclick="loadCategory(\'' . htmlspecialchars($nomDB) . '\')">';
                        if ($imatge) {
                            echo '<div class="category-image">';
                            echo '<img src="' . htmlspecialchars($imatge) . '" alt="' . htmlspecialchars($nomDB) . '">';
                            echo '</div>';
                        }
                        echo '<h3 class="category-name">' . htmlspecialchars($nomDB) . '</h3>';
                        if ($descripcio) {
                            echo '<p class="category-description">' . htmlspecialchars($descripcio) . '</p>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<p style="text-align: center;">No hi ha categories disponibles.</p>';
                }
                ?>
            </div>
        </section>
        
        <!-- Contenedor dinámico para productos y detalles (AJAX) -->
        <section id="dynamic-content" style="display: none;">
            <div id="ajax-results"></div>
        </section>
    </main>

    <button id="cart-float-btn" class="cart-float-btn">🛒 <span class="cart-count" id="cart-count">0</span></button>
    <div id="cart-overlay" class="cart-overlay"></div>
    <div id="cart-sidebar" class="cart-sidebar">
        <button id="close-cart" class="close-cart-btn">×</button>
        <h2>Cistella</h2>
        <div class="cart-content" id="cart-items"><p>La cistella està buida</p></div>
        <div class="cart-footer">
            <div class="cart-total"><strong>Total:</strong> <span id="cart-total">0.00€</span></div>
            <button class="btn-primary" onclick="checkout()">Finalitzar compra</button>
        </div>
    </div>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>