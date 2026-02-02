<?php
// Vista: Carretó lateral (dins del footer)
?>

<div class="cart-items-list">
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
            <div class="cart-item">
                <div class="item-info">
                    <h5><?= htmlspecialchars($item['nom']) ?></h5>
                    <p class="item-price"><?= $item['preu'] ?>€ x <span class="item-qty"><?= $item['quantitat'] ?></span></p>
                </div>
                <button onclick="removeFromCart(<?= $index ?>)" class="btn-remove">✕</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-cart">El carretó està buit</p>
    <?php endif; ?>
</div>
