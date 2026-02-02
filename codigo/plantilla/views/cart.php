<?php
// Vista: Carretó de compra

require __DIR__ . '/partials/header.php';
?>

<h1>Carretó de Compra</h1>

<div class="cart-container">
    <table id="cartTable" class="cart-table">
        <thead>
            <tr>
                <th>Producte</th>
                <th>Preu</th>
                <th>Quantitat</th>
                <th>Subtotal</th>
                <th>Acció</th>
            </tr>
        </thead>
        <tbody id="cartBody">
            <!-- Omplert dinàmicament per JavaScript -->
        </tbody>
    </table>
    
    <div class="cart-summary">
        <h3>Resum del carretó</h3>
        <p>Items totals: <strong id="totalItems">0</strong></p>
        <p>Total: <strong id="totalPrice">0€</strong></p>
        
        <div class="cart-actions">
            <a href="index.php?action=home" class="btn btn-secondary">Continuar comprant</a>
            <button onclick="buidarCart()" class="btn btn-danger">Buidar carretó</button>
            
            <?php if (isset($_SESSION['usuari'])): ?>
                <button onclick="finalizarCompra()" class="btn btn-success">Finalitzar compra</button>
            <?php else: ?>
                <p class="warning">Debes <a href="index.php?action=login">iniciar sessió</a> per finalitzar la compra</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Cargar carretó al cargar la pàgina
    window.addEventListener('DOMContentLoaded', function() {
        renderCartTable();
    });
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
