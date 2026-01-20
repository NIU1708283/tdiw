<?php
// Ocultar el sidebar en las páginas "Inici" (home) y "Cistella" (cart)
$action = $_GET['action'] ?? '';
if (!in_array($action, ['home', 'cistella', 'cart'])):
?>
<!-- Carrito Lateral Flotante -->
<button id="cart-float-btn" class="cart-float-btn" aria-label="Obrir cistella">
    <span class="cart-icon">🛒</span>
    <div class="cart-info">
        <span class="cart-count" id="cart-count">0</span>
        <span class="cart-price" id="cart-price">0.00€</span>
    </div>
</button>
<div id="cart-overlay" class="cart-overlay"></div>
<div id="cart-sidebar" class="cart-sidebar">
    <button id="close-cart" class="close-cart-btn" aria-label="Tancar cistella">×</button>
    <h2>La Meva Cistella</h2>
    <div class="cart-content" id="cart-items">
        <div style="text-align: center; padding: 60px 20px;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.3; margin: 0 auto 20px;">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <p style="margin: 0; color: var(--muted); font-size: 15px;">La cistella està buida</p>
        </div>
    </div>
    <div class="cart-footer">
        <div class="cart-total"><strong>Total:</strong> <span id="cart-total">0.00€</span></div>
        <button class="btn-primary" onclick="goToCart()">Veure Cistella Completa</button>
    </div>
</div>
<?php endif; ?>
