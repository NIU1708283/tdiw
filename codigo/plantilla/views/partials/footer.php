<?php
// Vista: Footer
?>

</main>

<!-- CARRETÓ SIDEBAR -->
<div id="cartOverlay" class="cart-overlay" onclick="window.closeCartGlobal()"></div>
<div id="cartSidebar" class="cart-sidebar">
    <div class="cart-header">
        <h3>Carretó de Compra</h3>
        <button onclick="window.closeCartGlobal()">✕</button>
    </div>
    <div id="cartItems" class="cart-items">
        <!-- Omplert dinàmicament per JavaScript -->
    </div>
    <div class="cart-footer">
        <div class="cart-total">
            <strong>Total: <span id="cart-total">0€</span></strong>
        </div>
        <a href="index.php?action=carretó" class="btn btn-primary btn-block">Anar al carretó</a>
        <?php if (isset($_SESSION['usuari'])): ?>
            <button onclick="finalizarCompra()" class="btn btn-success btn-block">Confirmar compra</button>
        <?php else: ?>
            <a href="index.php?action=login" class="btn btn-warning btn-block">Inicia sessió per comprar</a>
        <?php endif; ?>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <p>&copy; 2024 Botiga Online - TDIW. Tots els drets reservats.</p>
</footer>

<script src="script.js"></script>
</body>
</html>
