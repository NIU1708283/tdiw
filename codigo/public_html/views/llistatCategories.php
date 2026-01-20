<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Botiga - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=24.0">
    
    <script src="script.js?v=24.0" defer></script>
</head>

<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    
    <div>
        <hr>
    </div>
    
    <main>
        
        <section id="categories-section">
            <h1 style="text-align: center; margin: 2rem 0; color: #ff4500;">Botiga - Selecciona una Categoria</h1>
            
            <!-- Buscador global (mismo formato que en categorías) -->
            <div class="shop-bar-container" style="margin-bottom: 40px;">
                <div class="shop-bar">
                    <form id="global-search-form" class="search-form-bar" style="flex: 1;">
                        <input type="search" id="global-search-input" placeholder="Buscar productes..." class="search-input-bar">
                        <button type="submit" class="search-btn-bar">Buscar</button>
                    </form>
                </div>
            </div>
            
            <div class="categories-grid">
                <?php 
                if ($resultat_categories && count($resultat_categories) > 0) {
                    // Ordenar de la Z a la A
                    usort($resultat_categories, function($a, $b) {
                        return strcmp($b['nom'], $a['nom']);
                    });
                    
                    foreach($resultat_categories as $categoria) {
                        $nomDB = htmlspecialchars($categoria['nom'], ENT_QUOTES, 'UTF-8');
                        $imatge = htmlspecialchars($categoria['images'] ?: '', ENT_QUOTES, 'UTF-8');
                        $descripcio = htmlspecialchars($categoria['descripcio'] ?: '', ENT_QUOTES, 'UTF-8');
                        
                        // Tarjeta clickeable con AJAX - usar json_encode para pasar el nombre sin problemas
                        $nomJSON = json_encode($categoria['nom']);
                        echo '<div class="category-card" onclick="loadCategory(' . $nomJSON . ')">';
                        if ($imatge) {
                            echo '<div class="category-image">';
                            echo '<img src="' . $imatge . '" alt="' . $nomDB . '">';
                            echo '</div>';
                        }
                        echo '<h3 class="category-name">' . $nomDB . '</h3>';
                        if ($descripcio) {
                            echo '<p class="category-description">' . $descripcio . '</p>';
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

    <?php require __DIR__ . '/partials/cart-sidebar.php'; ?>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>