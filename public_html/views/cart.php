<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Meva Cistella - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=10.0">
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <main>
        <section class="container-content">
            <h2>La Meva Cistella</h2>
            <div class="cart-actions">
                <button class="btn-buidar" onclick="buidarCart()">Buidar Cabàs</button>
    
                <button class="btn-checkout" onclick="checkout()">Finalitzar Compra</button>
            </div>
        </section>
    </main>
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
