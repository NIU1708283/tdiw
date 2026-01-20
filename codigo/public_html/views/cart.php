<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Meva Cistella - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=10.0">
    <script src="script.js?v=10.0" defer></script>
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <div>
        <hr>
    </div>

    <main>
        <section class="cart-page-container">
            <h2 class="cart-page-title">La Meva Cistella</h2>
            
            <div id="cart-table-container">
                <!-- La taula es generada per JavaScript -->
            </div>
            
            <div class="cart-page-actions">
                <button class="btn-buidar-cart" onclick="buidarCart()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Buidar Cabàs
                </button>
                <button class="btn-finalitzar-compra" onclick="checkout()">
                    Finalitzar Compra
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </div>
        </section>
    </main>
    
    <script>
        // Generar taula del carrito quan es carregui la pàgina
        window.addEventListener('DOMContentLoaded', function() {
            renderCartTable();
        });
        
        function renderCartTable() {
            const container = document.getElementById('cart-table-container');
            
            fetch('index.php?action=cart_obtenir', { 
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.cart && data.cart.length > 0) {
                    let html = `
                    <div class="cart-page-table-wrapper">
                        <table class="cart-page-table">
                            <thead>
                                <tr>
                                    <th>Producte</th>
                                    <th>Quantitat</th>
                                    <th>Preu Unit.</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    let total = 0;
                    data.cart.forEach((item, index) => {
                        const subtotal = item.preu * item.quantitat;
                        total += subtotal;
                        html += `
                            <tr class="cart-page-item">
                                <td class="cart-page-product">
                                    <div class="cart-page-product-info">
                                        <div class="cart-page-product-image">
                                            <img src="${item.imatge}" alt="${item.nom}">
                                        </div>
                                        <div class="cart-page-product-name">${item.nom}</div>
                                    </div>
                                </td>
                                <td class="cart-page-quantity">
                                    <div class="quantity-control">
                                        <button class="qty-btn" onclick="updateQuantity(${index}, ${item.quantitat - 1})"><span>−</span></button>
                                        <input type="number" min="1" value="${item.quantitat}" 
                                               onchange="updateQuantity(${index}, this.value)">
                                        <button class="qty-btn" onclick="updateQuantity(${index}, ${item.quantitat + 1})"><span>+</span></button>
                                    </div>
                                </td>
                                <td class="cart-page-price">${item.preu.toFixed(2)}€</td>
                                <td class="cart-page-subtotal">${subtotal.toFixed(2)}€</td>
                                <td class="cart-page-remove">
                                    <button onclick="removeFromCartPage(${index})" class="btn-remove-item" aria-label="Eliminar">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="cart-page-total">
                        <span>Total:</span>
                        <span class="total-amount">${total.toFixed(2)}€</span>
                    </div>
                    `;
                    
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="cart-page-empty">
                            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            <p>La cistella està buida</p>
                            <a href="index.php?action=botiga" class="btn-shop-now">Anar a la Botiga</a>
                        </div>
                    `;
                }
            });
        }
        
        function removeFromCartPage(index) {
            const fd = new FormData();
            fd.append('index', index);
            
            fetch('index.php?action=cart_eliminar', { 
                method: 'POST', 
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    if (typeof window.updateCartUI === 'function') {
                        window.updateCartUI(d.cart, d.count);
                    }
                    renderCartTable();
                }
            });
        }
        
        function updateQuantity(index, newQuantity) {
            const qty = parseInt(newQuantity);
            if (qty < 1) {
                alert('La quantitat ha de ser almenys 1');
                renderCartTable();
                return;
            }
            
            const fd = new FormData();
            fd.append('index', index);
            fd.append('quantitat', qty);
            
            fetch('index.php?action=cart_modificar', { 
                method: 'POST', 
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    if (typeof window.updateCartUI === 'function') {
                        window.updateCartUI(d.cart, d.count);
                    }
                    renderCartTable();
                } else {
                    alert('Error al actualizar la quantitat');
                    renderCartTable();
                }
            })
            .catch(err => {
                console.error('Error:', err);
                renderCartTable();
            });
        }
    </script>
    
    <?php require __DIR__ . '/partials/cart-sidebar.php'; ?>
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
