<main class="container-content">
    <h2>Confirmació de la teva comanda</h2>
    
    <section class="resum-enviament">
        <h3>Dades d'enviament</h3>
        <p><strong>Nom:</strong> <?= htmlspecialchars($dadesEnviament['nom']) ?></p>
        <p><strong>Adreça:</strong> <?= htmlspecialchars($dadesEnviament['adreca'] . ", " . $dadesEnviament['poblacio']) ?></p>
    </section>

    <table class="resum-productes">
        <thead>
            <tr><th>Producte</th><th>Quantitat</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nom']) ?></td>
                    <td><?= $item['quantitat'] ?></td>
                    <td><?= number_format($item['preu'] * $item['quantitat'], 2) ?>€</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="accions-confirmacio">
        <p><strong>Total a pagar: <?= number_format($total, 2) ?>€</strong></p>
        <form action="index.php?action=finalitzar-compra" method="post">
            <button type="submit" class="btn-confirmar">Confirmar i Pagar</button>
        </form>
    </div>
</main>