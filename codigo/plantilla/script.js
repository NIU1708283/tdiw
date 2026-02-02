/* ===== AJAX/Fetch - CARRETÓ ===== */

// Inicialitzar carretó al carregar
window.addEventListener('DOMContentLoaded', function() {
    loadCartFromSession();
    setupDropdownMenu();
});

// Cargar carretó des de la sessió
function loadCartFromSession() {
    fetch('index.php?action=home', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .catch(() => {
        // Si falla, carretó estarà buit
        updateCartUI([], 0);
    });
}

// Afegir al carretó (AJAX)
window.addToCartAJAX = function(id, nom, preu, imatge) {
    const quantitat = document.getElementById(`qty-${id}`)?.value || 1;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('nom', nom);
    formData.append('preu', preu);
    formData.append('imatge', imatge);
    formData.append('quantitat', quantitat);
    
    fetch('index.php?action=cart_afegir', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartUI(data.cart, data.count);
            showCartNotification(`${nom} afegit al carretó!`);
        }
    })
    .catch(err => console.error('Error:', err));
};

// Mostrar notificació
function showCartNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Eliminar del carretó
window.removeFromCart = function(index) {
    const formData = new FormData();
    formData.append('index', index);
    
    fetch('index.php?action=cart_eliminar', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartUI(data.cart, data.count);
            renderCartTable(); // Si estem a la pàgina del carretó
        }
    })
    .catch(err => console.error('Error:', err));
};

// Modificar quantitat
window.modifyCartItem = function(index, quantitat) {
    const formData = new FormData();
    formData.append('index', index);
    formData.append('quantitat', quantitat);
    
    fetch('index.php?action=cart_modificar', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartUI(data.cart, data.count);
            renderCartTable();
        }
    })
    .catch(err => console.error('Error:', err));
};

// Buidar carretó
window.buidarCart = function() {
    if (!confirm('Estàs segur que vols buidar el carretó?')) return;
    
    fetch('index.php?action=cart_buidar', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCartUI([], 0);
            renderCartTable();
        }
    })
    .catch(err => console.error('Error:', err));
};

// Actualizar UI del carretó (sidebar + comptador)
window.updateCartUI = function(cart, count) {
    // Actualitzar comptador
    const countEl = document.getElementById('cart-count');
    if (countEl) {
        countEl.textContent = count;
    }
    
    // Calcular total
    let total = 0;
    cart.forEach(item => {
        total += item.preu * item.quantitat;
    });
    
    const totalEl = document.getElementById('cart-total');
    if (totalEl) {
        totalEl.textContent = total.toFixed(2) + '€';
    }
    
    // Renderitzar items
    const cartItemsEl = document.getElementById('cartItems');
    if (cartItemsEl) {
        if (cart.length === 0) {
            cartItemsEl.innerHTML = '<p class="empty-cart">El carretó està buit</p>';
        } else {
            cartItemsEl.innerHTML = cart.map((item, index) => `
                <div class="cart-item">
                    <div class="item-info">
                        <h5>${item.nom}</h5>
                        <p>${item.preu}€ x ${item.quantitat} = ${(item.preu * item.quantitat).toFixed(2)}€</p>
                    </div>
                    <button onclick="removeFromCart(${index})" class="btn-remove">✕</button>
                </div>
            `).join('');
        }
    }
};

// Obrir/tancar sidebar del carretó
window.openCartGlobal = function() {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('cartOverlay');
    if (sidebar) sidebar.classList.add('active');
    if (overlay) overlay.classList.add('active');
};

window.closeCartGlobal = function() {
    const sidebar = document.getElementById('cartSidebar');
    const overlay = document.getElementById('cartOverlay');
    if (sidebar) sidebar.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
};

// Finalizar compra
window.finalizarCompra = function() {
    window.location.href = 'index.php?action=finalitzar_compra';
};

/* ===== AJAX/Fetch - CATEGORIES I PRODUCTES ===== */

// Cargar categoria amb AJAX
window.loadCategory = function(categoryId) {
    fetch(`index.php?action=categoria&cat=${categoryId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderCategoryProducts(data.data);
            document.getElementById('productosSection').style.display = 'block';
            document.getElementById('productosSection').scrollIntoView({ behavior: 'smooth' });
        }
    })
    .catch(err => console.error('Error:', err));
};

// Renderitzar productes d'una categoria
function renderCategoryProducts(productes) {
    const container = document.getElementById('productosContainer');
    if (!container) return;
    
    if (productes.length === 0) {
        container.innerHTML = '<p>No hi ha productes en aquesta categoria</p>';
        return;
    }
    
    container.innerHTML = productes.map(prod => `
        <div class="product-card">
            ${prod.images ? `<img src="${prod.images}" alt="${prod.nom}">` : '<div class="image-placeholder">📦</div>'}
            <h4 onclick="loadProductDetail(${prod.id})">${prod.nom}</h4>
            <p class="price">${prod.preu}€</p>
            <button onclick="addToCartAJAX(${prod.id}, '${prod.nom}', ${prod.preu}, '${prod.images}')" class="btn btn-sm">Afegir al carretó</button>
        </div>
    `).join('');
}

// Caregar detall de producte (modal)
window.loadProductDetail = function(productId) {
    fetch(`index.php?action=producte&id=${productId}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showProductDetail(data.data);
        }
    })
    .catch(err => console.error('Error:', err));
};

// Mostrar detall en modal
function showProductDetail(producto) {
    const content = document.getElementById('productDetailContent');
    const modal = document.getElementById('productDetailModal');
    
    content.innerHTML = `
        <div class="product-detail">
            ${producto.images ? `<img src="${producto.images}" alt="${producto.nom}">` : '<div class="image-placeholder large">📦</div>'}
            <h2>${producto.nom}</h2>
            <p class="description">${producto.descripcio}</p>
            <p class="price">${producto.preu}€</p>
            <div class="product-actions">
                <input type="number" id="qty-${producto.id}" min="1" value="1">
                <button onclick="addToCartAJAX(${producto.id}, '${producto.nom}', ${producto.preu}, '${producto.images}')" class="btn btn-primary">Afegir al carretó</button>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
}

// Tancar modal
window.closeProductDetail = function() {
    const modal = document.getElementById('productDetailModal');
    if (modal) modal.style.display = 'none';
};

/* ===== AJAX/Fetch - CERCA ===== */

// Buscar en categoria
window.searchCategoryProducts = function() {
    const query = document.getElementById('categorySearch')?.value || '';
    // Implementar si és necessari
};

// Búsqueda global AJAX
window.performGlobalSearch = function(query) {
    if (query.length < 2) {
        document.getElementById('searchResults').innerHTML = '';
        return;
    }
    
    fetch(`index.php?action=buscar&q=${encodeURIComponent(query)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            renderSearchResults(data.data);
        }
    })
    .catch(err => console.error('Error:', err));
};

function renderSearchResults(resultados) {
    const container = document.getElementById('searchResults');
    if (!container) return;
    
    if (resultados.length === 0) {
        container.innerHTML = '<p>No s\'han trobat resultats</p>';
        return;
    }
    
    container.innerHTML = resultados.map(prod => `
        <div class="search-result" onclick="loadProductDetail(${prod.id})">
            <strong>${prod.nom}</strong> - ${prod.preu}€
        </div>
    `).join('');
}

/* ===== TAULA DEL CARRETÓ (pàgina cart.php) ===== */

function renderCartTable() {
    fetch('index.php?action=carretó', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        // Extraure el carretó de la sessió via PHP
        const cart = JSON.parse(sessionStorage.getItem('cart') || '[]');
        
        const tbody = document.getElementById('cartBody');
        if (!tbody) return;
        
        if (cart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">El carretó està buit</td></tr>';
            document.getElementById('totalItems').textContent = '0';
            document.getElementById('totalPrice').textContent = '0€';
            return;
        }
        
        let total = 0;
        let items = 0;
        
        tbody.innerHTML = cart.map((item, index) => {
            const subtotal = item.preu * item.quantitat;
            total += subtotal;
            items += item.quantitat;
            
            return `
                <tr>
                    <td>${item.nom}</td>
                    <td>${item.preu}€</td>
                    <td>
                        <input type="number" min="1" value="${item.quantitat}" 
                               onchange="modifyCartItem(${index}, this.value)">
                    </td>
                    <td>${subtotal.toFixed(2)}€</td>
                    <td>
                        <button onclick="removeFromCart(${index})" class="btn btn-sm btn-danger">Eliminar</button>
                    </td>
                </tr>
            `;
        }).join('');
        
        document.getElementById('totalItems').textContent = items;
        document.getElementById('totalPrice').textContent = total.toFixed(2) + '€';
    });
}

/* ===== UTILITATS ===== */

// Dropdown menu
function setupDropdownMenu() {
    const toggles = document.querySelectorAll('.dropdown-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const content = this.nextElementSibling;
            if (content && content.classList.contains('dropdown-content')) {
                content.classList.toggle('show');
            }
        });
    });
    
    // Tancar dropdown al fer click fora
    document.addEventListener('click', function(e) {
        toggles.forEach(toggle => {
            const content = toggle.nextElementSibling;
            if (content && !toggle.contains(e.target) && !content.contains(e.target)) {
                content.classList.remove('show');
            }
        });
    });
}

// Logout AJAX
function logoutAJAX() {
    fetch('index.php?action=logout', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            window.location.reload();
        }
    })
    .catch(err => console.error('Error:', err));
}

// Tancar modal amb ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProductDetail();
        closeCartGlobal();
    }
});
