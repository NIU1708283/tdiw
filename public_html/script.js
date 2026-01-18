/**
 * script.js - ToonTunes
 * jQuery Menu, Mode Fosc, Carret i AJAX Shop
 */
'use strict';

// 1. JQUERY MENU
(function($){
    $(function(){
        var $btn = $('.dropdown-toggle');
        var $menu = $('#userDropdown');
        $btn.on('click', function(e){
            e.preventDefault();
            $menu.toggleClass('show');
        });
        $(document).on('click', function(e){
            if(!$(e.target).closest('.user-dropdown').length){
                $menu.removeClass('show');
            }
        });
    });
})(jQuery);

// 2. MODE FOSC
(function(){
    var storageKey = 'tt_dark_mode';
    var toggleId = 'dark-toggle';

    function applyDark(enabled){
        var root = document.documentElement;
        if(enabled) root.classList.add('dark'); else root.classList.remove('dark');
        var btn = document.getElementById(toggleId);
        if(btn) {
            btn.setAttribute('aria-pressed', enabled ? 'true' : 'false');
            btn.textContent = enabled ? '☀️' : '🌙';
        }
    }
    function initDarkToggle(){
        var enabled = localStorage.getItem(storageKey) === '1';
        applyDark(enabled);
        document.addEventListener('click', function(e){
            if(e.target.closest('#' + toggleId)) {
                e.preventDefault();
                enabled = !enabled;
                document.documentElement.classList.toggle('dark');
                localStorage.setItem(storageKey, enabled ? '1' : '0');
                applyDark(enabled);
            }
        });
    }
    if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initDarkToggle);
    else initDarkToggle();
})();

// 3. CARRET
(function(){
    function initCartSidebar(){
        var cartBtn = document.getElementById('cart-float-btn');
        var cartSidebar = document.getElementById('cart-sidebar');
        var cartOverlay = document.getElementById('cart-overlay');
        var closeBtn = document.getElementById('close-cart');

        if(!cartBtn) return;

        window.openCartGlobal = function() {
            cartSidebar.classList.add('active');
            cartOverlay.classList.add('active');
        };
        
        function closeCart() {
            cartSidebar.classList.remove('active');
            cartOverlay.classList.remove('active');
        }
        
        cartBtn.onclick = function(e) { e.preventDefault(); window.openCartGlobal(); };
        if(closeBtn) closeBtn.onclick = function(e) { e.preventDefault(); closeCart(); };
        if(cartOverlay) cartOverlay.onclick = function(e) { e.preventDefault(); closeCart(); };
    }

    window.updateCartUI = function(cart, count){
        var countEl = document.getElementById('cart-count');
        var itemsEl = document.getElementById('cart-items');
        var totalEl = document.getElementById('cart-total');
        if(countEl) countEl.textContent = count;
        if(itemsEl && totalEl){
            var html = '', total = 0;
            if(!cart || cart.length === 0) html = '<p>La cistella està buida</p>';
            else {
                cart.forEach(function(item, index){
                    total += item.preu * item.quantitat;
                    html += `<div class="cart-item">
                        <div class="cart-item-image"><img src="${item.imatge}" alt="${item.nom}"></div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">${item.nom}</div>
                            <div class="cart-item-quantity">x ${item.quantitat}</div>
                            <div class="cart-item-price">${formatPrice(item.preu * item.quantitat)}€</div>
                        </div>
                        <button class="cart-item-remove" onclick="removeFromCart(${index})">×</button>
                    </div>`;
                });
            }
            itemsEl.innerHTML = html;
            totalEl.textContent = formatPrice(total) + '€';
        }
    };

    window.removeFromCart = function(index){
        var fd = new FormData(); fd.append('index', index);
        fetch('index.php?action=cart_eliminar', { method: 'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) window.updateCartUI(d.cart, d.count); });
    };

    window.checkout = function(){
        fetch('index.php?action=cart_buidar', { method: 'POST' })
        .then(r=>r.json()).then(d=>{ 
            if(d.success){ 
                window.updateCartUI([], 0); 
                alert('Compra finalitzada!'); 
                document.getElementById('cart-sidebar').classList.remove('active'); 
                document.getElementById('cart-overlay').classList.remove('active'); 
            }
        });
    };
    
    // Càrrega inicial del carret
    fetch('index.php?action=cart_obtenir', { method: 'POST' })
        .then(r=>r.json()).then(d=>{ if(d.success) window.updateCartUI(d.cart, d.count); });

    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCartSidebar);
    } else {
        initCartSidebar();
    }
})();

// 4. BOTIGA AJAX
let currentCategorySlug = null;

function formatPrice(price) {
    return parseFloat(price).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

window.loadCategory = function(catSlug) {
    currentCategorySlug = catSlug;
    const container = document.getElementById('ajax-results');
    document.getElementById('categories-section').style.display = 'none';
    document.getElementById('dynamic-content').style.display = 'block';
    container.innerHTML = '<div style="text-align:center; padding:50px;"><p>Carregant...</p></div>';

    fetch(`index.php?action=categoria&cat=${catSlug}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            renderCategoryProducts(data.category, data.products);
        }
    });
};

function renderCategoryProducts(categoryName, products) {
    const container = document.getElementById('ajax-results');
    let html = `<h1 style="text-align: center; margin: 1.5rem 0 2rem 0; color: #ff4500;">${categoryName}</h1>
    <div class="shop-bar-container"><div class="shop-bar">
        <a href="#" onclick="showCategories(); return false;" class="back-btn-bar">← Categories</a>
        <form id="search-form-ajax" class="search-form-bar">
            <input type="search" id="search-input-ajax" class="search-input-bar" placeholder="Cerca...">
            <button type="submit" class="search-btn-bar">Cerca</button>
        </form>
    </div></div>
    <div class="products-grid">`;
    
    if(products.length > 0) {
        products.forEach(prod => {
            html += `<div class="product-card">
                <div onclick="loadProductDetail(${prod.id})" style="cursor: pointer;">
                    <div class="product-image"><img src="${prod.imatge}" alt="${prod.nom}"></div>
                    <h3 class="product-name">${prod.nom}</h3>
                    <p class="product-price">${formatPrice(prod.preu)}€</p>
                </div>
                <button class="btn-add-to-cart"
                    onclick="addToCartAJAX(${prod.id}, '${prod.nom.replace(/'/g, "\\'")}', ${prod.preu}, '${prod.imatge}'); event.stopPropagation();">
                    Afegir a la cistella
                </button>
            </div>`;
        });
    } else {
        html += '<p style="text-align:center; grid-column:1/-1">No hi ha productes.</p>';
    }
    html += '</div>';
    container.innerHTML = html;
    
    // Agregar event listener al formulario de búsqueda
    const searchForm = document.getElementById('search-form-ajax');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchQuery = document.getElementById('search-input-ajax').value;
            searchProducts(currentCategorySlug, searchQuery);
        });
    }
}

function searchProducts(category, query) {
    const container = document.getElementById('ajax-results');
    const resultsContainer = container.querySelector('.products-grid');
    if(resultsContainer) {
        resultsContainer.innerHTML = '<p style="text-align:center; grid-column:1/-1; padding:50px;">Cercant...</p>';
    }
    
    let url = `index.php?action=categoria&cat=${encodeURIComponent(category)}`;
    if(query && query.trim() !== '') {
        url += `&q=${encodeURIComponent(query)}`;
    }
    
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            let html = '';
            if(data.products.length > 0) {
                data.products.forEach(prod => {
                    html += `<div class="product-card">
                        <div onclick="loadProductDetail(${prod.id})" style="cursor: pointer;">
                            <div class="product-image"><img src="${prod.imatge}" alt="${prod.nom}"></div>
                            <h3 class="product-name">${prod.nom}</h3>
                            <p class="product-price">${formatPrice(prod.preu)}€</p>
                        </div>
                        <button class="btn-add-to-cart"
                            onclick="addToCartAJAX(${prod.id}, '${prod.nom.replace(/'/g, "\\'")}', ${prod.preu}, '${prod.imatge}'); event.stopPropagation();">
                            Afegir a la cistella
                        </button>
                    </div>`;
                });
            } else {
                html = '<p style="text-align:center; grid-column:1/-1">No s\'han trobat productes' + 
                       (query ? ' per a "' + query + '"' : '') + '.</p>';
            }
            if(resultsContainer) {
                resultsContainer.innerHTML = html;
            }
        }
    });
}

window.loadProductDetail = function(id) {
    const container = document.getElementById('ajax-results');
    container.innerHTML = '<div style="text-align:center; padding:50px;"><p>Carregant detall...</p></div>';
    fetch(`index.php?action=producte&id=${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            const p = data.product;
            container.innerHTML = `<div class="product-detail-container">
                <a href="#" onclick="loadCategory('${currentCategorySlug}'); return false;" class="back-btn" style="margin: 20px 0;">← Tornar</a>
                <div class="product-detail-content">
                    <div class="product-detail-image"><img src="${p.imatge}" alt="${p.nom}"></div>
                    <div class="product-detail-info">
                        <h1 class="product-detail-title">${p.nom}</h1>
                        <p class="product-detail-description">${p.descripcio}</p>
                        <p class="product-detail-price">${formatPrice(p.preu)}€</p>
                        <div class="product-detail-actions">
                            <div class="quantity-selector"><label>Quantitat:</label><input type="number" id="qty-${p.id}" min="1" value="1"></div>
                            <button class="btn-add-to-cart" onclick="addToCartAJAX(${p.id}, '${p.nom.replace(/'/g, "\\'")}', ${p.preu}, '${p.imatge}')">Afegir a la cistella</button>
                        </div>
                    </div>
                </div>
            </div>`;
        }
    });
};

window.showCategories = function() {
    document.getElementById('dynamic-content').style.display = 'none';
    document.getElementById('categories-section').style.display = 'block';
    document.getElementById('ajax-results').innerHTML = '';
    currentCategorySlug = null;
};

window.addToCartAJAX = function(id, nom, preu, imatge) {
    const qtyInput = document.getElementById(`qty-${id}`);
    const qty = qtyInput ? parseInt(qtyInput.value) : 1;
    const fd = new FormData();
    fd.append('id', id);
    fd.append('nom', nom);
    fd.append('preu', preu);
    fd.append('imatge', imatge);
    fd.append('quantitat', qty);
    
    fetch('index.php?action=cart_afegir', { method: 'POST', body: fd })
        .then(r=>r.json())
        .then(d=>{ 
            if(d.success) {
                window.updateCartUI(d.cart, d.count);
                window.openCartGlobal();
            }
        });
};

window.buidarCart = function() {
    if (confirm('Estàs segur que vols buidar tot el cabàs?')) {
        fetch('index.php?action=cart_buidar', { method: 'POST' })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    // Actualitzem la UI usant la funció que ja tens definida
                    window.updateCartUI([], 0);
                    alert('El cabàs s\'ha buidat correctament.');
                }
            });
    }
};

