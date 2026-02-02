/**
 * script.js - ToonTunes
 * jQuery Menu, Mode Fosc, Carret i AJAX Shop
 */
'use strict';

// ---- Utilitats globals ----

// Función global para formatear precios
window.formatPrice = function(price) {
    return parseFloat(price).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

// Función para mostrar notificación verde
window.showCartNotification = function(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #4CAF50;
        color: white;
        padding: 16px 30px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        z-index: 9999;
        opacity: 1;
        transition: opacity 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.style.opacity = '0';
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 1000);
};

// ---- 1. Menu desplegable d'usuari (jQuery) ----
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

// ---- 2. Sistema de mode fosc ----
(function(){
    var storageKey = 'tt_dark_mode';
    var toggleId = 'dark-toggle';

    function applyDark(enabled){
        var root = document.documentElement;
        if(enabled) root.classList.add('dark'); else root.classList.remove('dark');
        var btn = document.getElementById(toggleId);
        if(btn) {
            btn.setAttribute('aria-pressed', enabled ? 'true' : 'false');
            btn.textContent = enabled ? '🌙' : '☀️';
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

// ---- 3. Carret lateral ----
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
        var priceEl = document.getElementById('cart-price');
        var itemsEl = document.getElementById('cart-items');
        var totalEl = document.getElementById('cart-total');
        
        // Actualizar contador
        if(countEl) {
            countEl.textContent = count;
        }
        
        if(itemsEl && totalEl){
            var html = '';
            var total = 0;
            if(!cart || cart.length === 0) {
                html = `<div style="text-align: center; padding: 60px 20px;">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.3; margin: 0 auto 20px;">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <p style="margin: 0; color: var(--muted); font-size: 15px;">La cistella està buida</p>
                </div>`;
            }
            else {
                cart.forEach(function(item, index){
                    total += parseFloat(item.preu) * parseInt(item.quantitat);
                    const itemTotal = window.formatPrice(parseFloat(item.preu) * parseInt(item.quantitat));
                    html += `<div class="cart-item">
                        <div class="cart-item-image"><img src="${item.imatge}" alt="${item.nom}"></div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">${item.nom}</div>
                            <div class="cart-item-quantity">x ${item.quantitat}</div>
                            <div class="cart-item-price">${itemTotal}€</div>
                        </div>
                        <button class="cart-item-remove" onclick="removeFromCart(${index})" aria-label="Eliminar">×</button>
                    </div>`;
                });
            }
            itemsEl.innerHTML = html;
            totalEl.textContent = window.formatPrice(total) + '€';
            
            // Actualizar precio en el botón flotante copiando el valor del sidebar
            if(priceEl) {
                priceEl.textContent = totalEl.textContent;
            }
        }
    };

    window.removeFromCart = function(index){
        var fd = new FormData(); fd.append('index', index);
        fetch('index.php?action=cart_eliminar', { method: 'POST', body: fd })
        .then(r=>r.json()).then(d=>{ if(d.success) window.updateCartUI(d.cart, d.count); });
    };

    window.checkout = function(){
    window.location.href = 'index.php?action=finalitzar_compra';
    };

    window.goToCart = function(){
    window.location.href = 'index.php?action=cistella';
    };
    
    // Càrrega inicial del carret
    fetch('index.php?action=cart_obtenir', { method: 'POST' })
        .then(r=>r.json()).then(d=>{ 
            if(d.success) {
                window.updateCartUI(d.cart, d.count);
            }
        });

    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCartSidebar);
    } else {
        initCartSidebar();
    }
})();

// ---- 4. Botiga amb navegació AJAX ----
let currentCategorySlug = null;

window.loadCategory = function(catSlug) {
    currentCategorySlug = catSlug;
    const container = document.getElementById('ajax-results');
    document.getElementById('categories-section').style.display = 'none';
    document.getElementById('dynamic-content').style.display = 'block';
    container.innerHTML = '<div style="text-align:center; padding:50px;"><p>Carregant...</p></div>';

    console.log('Loading category:', catSlug);
    
    fetch(`index.php?action=categoria&cat=${encodeURIComponent(catSlug)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => {
        console.log('Response status:', res.status);
        return res.json();
    })
    .then(data => {
        console.log('Data received:', data);
        if(data.success) {
            console.log('Success! Rendering products...');
            renderCategoryProducts(data.category, data.products);
        } else {
            console.error('Error in response:', data.error);
            container.innerHTML = '<div style="text-align:center; color:red;">Error: ' + (data.error || 'Unknown error') + '</div>';
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
        container.innerHTML = '<div style="text-align:center; color:red;">Error de connexió: ' + err + '</div>';
    });
};

function renderCategoryProducts(categoryName, products) {
    console.log('renderCategoryProducts called with:', { categoryName, productsCount: products ? products.length : 0, products });
    
    const container = document.getElementById('ajax-results');
    // Escapar categoryName per seguretat contra XSS
    const div = document.createElement('div');
    div.textContent = categoryName;
    const safeCategoryName = div.innerHTML;
    let html = `<h1 style="text-align: center; margin: 1.5rem 0 2rem 0; color: #ff4500;">${safeCategoryName}</h1>
    <div class="shop-bar-container"><div class="shop-bar">
        <a href="#" onclick="showCategories(); return false;" class="back-btn-bar">← Categories</a>
        <form id="search-form-ajax" class="search-form-bar">
            <input type="search" id="search-input-ajax" class="search-input-bar" placeholder="Cerca...">
            <button type="submit" class="search-btn-bar">Cerca</button>
        </form>
    </div></div>
    <div class="products-grid">`;
    
    if(products && products.length > 0) {
        console.log('Rendering', products.length, 'products');
        products.forEach(prod => {
            // Escapar valors per seguretat contra XSS
            const divNom = document.createElement('div');
            divNom.textContent = prod.nom;
            const safeProdNom = divNom.innerHTML;
            
            const divImg = document.createElement('div');
            divImg.textContent = prod.imatge;
            const safeProdImatge = divImg.innerHTML;
            
            const formattedPrice = window.formatPrice(prod.preu);
            
            html += `<div class="product-card">
                <div onclick="loadProductDetail(${prod.id})" style="cursor: pointer;">
                    <div class="product-image"><img src="${safeProdImatge}" alt="${safeProdNom}"></div>
                    <h3 class="product-name">${safeProdNom}</h3>
                    <p class="product-price">${formattedPrice}€</p>
                </div>
                <button class="btn-add-to-cart"
                    onclick="addToCartAJAX(${prod.id}, '${safeProdNom.replace(/'/g, "\\'")}', ${prod.preu}, '${safeProdImatge}'); event.stopPropagation();">
                    Afegir a la cistella
                </button>
            </div>`;
        });
    } else {
        console.log('No products found');
        html += '<p style="text-align:center; grid-column:1/-1">No hi ha productes.</p>';
    }
    html += '</div>';
    container.innerHTML = html;
    
    // Agregar event listener al formulario de búsqueda
    const searchForm = document.getElementById('search-form-ajax');
    const searchInput = document.getElementById('search-input-ajax');
    
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchQuery = searchInput.value.trim();
            searchProducts(currentCategorySlug, searchQuery);
        });
    }
    
    // Detectar cuando el campo de búsqueda está vacío para volver a mostrar todos los productos de la categoría
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            // Si el campo está completamente vacío, recargar todos los productos de la categoría
            if(this.value.trim() === '') {
                loadCategory(currentCategorySlug);
            }
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
                    const formattedPrice = window.formatPrice(prod.preu);
                    html += `<div class="product-card">
                        <div onclick="loadProductDetail(${prod.id})" style="cursor: pointer;">
                            <div class="product-image"><img src="${prod.imatge}" alt="${prod.nom}"></div>
                            <h3 class="product-name">${prod.nom}</h3>
                            <p class="product-price">${formattedPrice}€</p>
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
            const formattedDetailPrice = window.formatPrice(p.preu);
            container.innerHTML = `<div class="product-detail-container">
                <a href="#" onclick="loadCategory('${currentCategorySlug}'); return false;" class="back-btn" style="margin: 20px 0;">← Tornar</a>
                <div class="product-detail-content">
                    <div class="product-detail-image"><img src="${p.imatge}" alt="${p.nom}"></div>
                    <div class="product-detail-info">
                        <h1 class="product-detail-title">${p.nom}</h1>
                        <p class="product-detail-description">${p.descripcio}</p>
                        <p class="product-detail-price">${formattedDetailPrice}€</p>
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
                // Issue 8: Desactivat per no desplegar automàticament el sidebar
                // window.openCartGlobal();
                // Issue 9: Missatge de confirmació
                window.showCartNotification('✓ Producte afegit al cabàs correctament!');
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
                    // Issue 1: If we're on the cart page, update the table too
                    if (typeof renderCartTable === 'function') {
                        renderCartTable();
                    }
                }
            });
    }
};

// ---- Buscador global ----
(function() {
    const globalSearchForm = document.getElementById('global-search-form');
    const globalSearchInput = document.getElementById('global-search-input');
    
    if (globalSearchForm) {
        globalSearchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const query = globalSearchInput.value.trim();
            if (query.length < 2) {
                alert('Escriu almenys 2 caràcters per buscar');
                return;
            }
            performGlobalSearch(query);
        });
    }
    
    // Detectar cuando el campo de búsqueda global está vacío para volver a mostrar categorías
    if (globalSearchInput) {
        globalSearchInput.addEventListener('input', function() {
            // Si el campo está completamente vacío, volver a mostrar las categorías
            if (this.value.trim() === '') {
                const categoriesSection = document.getElementById('categories-section');
                
                // Verificar si el título indica que estamos en resultados de búsqueda
                if (categoriesSection) {
                    const title = categoriesSection.querySelector('h1');
                    const titleText = title ? title.textContent : '';
                    
                    // Si el título no es el original, significa que estamos en resultados de búsqueda
                    if (titleText && !titleText.includes('Selecciona una Categoria')) {
                        // Eliminar el botón de volver si existe
                        const backBtn = document.getElementById('back-search-btn');
                        if (backBtn) {
                            backBtn.remove();
                        }
                        
                        // Recargar la página para mostrar las categorías originales
                        location.reload();
                    }
                }
            }
        });
    }
})();

function performGlobalSearch(query) {
    const categoriesSection = document.getElementById('categories-section');
    const categoriesGrid = document.querySelector('.categories-grid');
    
    if (!categoriesGrid) return;
    
    categoriesGrid.innerHTML = '<p style="text-align:center; grid-column:1/-1; padding:50px;">Cercant productes...</p>';
    
    const url = `index.php?action=buscar&q=${encodeURIComponent(query)}`;
    
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            let html = '';
            if (data.products && data.products.length > 0) {
                // Mostrar mensaje de búsqueda
                if (categoriesSection) {
                    categoriesSection.querySelector('h1').textContent = `Resultats de la cerca: "${data.query}" (${data.products.length} producte${data.products.length !== 1 ? 's' : ''})`;
                    const backBtn = document.getElementById('back-search-btn');
                    if (!backBtn) {
                        const btn = document.createElement('button');
                        btn.id = 'back-search-btn';
                        btn.textContent = '← Tornar a categories';
                        btn.style.cssText = 'display:block; margin:20px auto; padding:10px 20px; background:#666; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold;';
                        btn.onclick = function() { location.reload(); };
                        categoriesSection.parentElement.insertBefore(btn, categoriesSection);
                    }
                }
                
                data.products.forEach(prod => {
                    const formattedSearchPrice = window.formatPrice(prod.preu);
                    html += `<div class="product-card" style="cursor:pointer;" onclick="loadProductDetail(${prod.id})">
                        <div>
                            <div class="product-image"><img src="${prod.imatge}" alt="${prod.nom}"></div>
                            <h3 class="product-name">${prod.nom}</h3>
                            <p style="color:#999; font-size:12px;">Categoria: ${prod.categoria || 'N/A'}</p>
                            <p class="product-price">${formattedSearchPrice}€</p>
                        </div>
                        <button class="btn-add-to-cart" onclick="event.stopPropagation(); addToCartQuick(${prod.id}, '${prod.nom}', ${prod.preu}, '${prod.imatge}')">Afegir al cabàs</button>
                    </div>`;
                });
            } else {
                if (categoriesSection) {
                    categoriesSection.querySelector('h1').textContent = `No s'han trobat productes per a "${data.query}"`;
                }
                html = '<p style="text-align:center; grid-column:1/-1; padding:50px; font-size:16px;">Prova amb altres paraules clau.</p>';
            }
            categoriesGrid.innerHTML = html;
        } else {
            categoriesGrid.innerHTML = '<p style="text-align:center; grid-column:1/-1; padding:50px; color:red;">Error en la cerca</p>';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        categoriesGrid.innerHTML = '<p style="text-align:center; grid-column:1/-1; padding:50px; color:red;">Error en la cerca</p>';
    });
}

function addToCartQuick(id, nom, preu, imatge) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('nom', nom);
    formData.append('preu', preu);
    formData.append('imatge', imatge);
    formData.append('quantitat', 1);
    
    fetch('index.php?action=cart_afegir', { 
        method: 'POST', 
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            window.updateCartUI(d.cart, d.count);
            window.showCartNotification('✓ Producte afegit al cabàs!');
        }
    });
}
