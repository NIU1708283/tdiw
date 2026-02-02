<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Redirigir si no està loguejat
if (!isset($_SESSION['usuari'])) {
    header('Location: index.php?action=iniciarsesio');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Meves Comandes - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=10.0">
    <script src="script.js?v=26.0" defer></script>
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    
    <div>
        <hr>
    </div>
    
    <main>
        <section class="perfil-container">
            <h2 class="perfil-title">Les Meves Comandes</h2>
            
            <?php if (empty($llistatComandes)): ?>
                <div class="empty-comandes-message">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity: 0.3;">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <p style="font-size: 18px; color: var(--text); margin: 20px 0 10px 0; font-weight: 600;">Encara no has realitzat cap comanda</p>
                    <p style="font-size: 14px; color: var(--muted); margin: 0 0 30px 0;">Explora la nostra botiga i troba els teus instruments favorits</p>
                    <a href="index.php?action=botiga" class="btn-comenca-comprar">Comença a Comprar</a>
                </div>
            <?php else: ?>
                <table class="comandes-table">
                    <thead>
                        <tr>
                            <th>ID Comanda</th>
                            <th>Data</th>
                            <th style="text-align: right;">Total</th>
                            <th style="text-align: center;">Estat</th>
                            <th style="text-align: center;">Accions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($llistatComandes as $c): ?>
                            <tr>
                                <td style="font-weight: bold;">#<?= htmlspecialchars($c['id']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($c['data_comanda'])) ?></td>
                                <td style="text-align: right; font-weight: bold;"><?= number_format((float)$c['import_total'], 2, ',', '.') ?>€</td>
                                <td style="text-align: center;">
                                    <?php
                                        $estatClass = strtolower(str_replace('·', '', $c['estat']));
                                        $estatClass = str_replace('á', 'a', $estatClass);
                                    ?>
                                    <span class="estat-badge <?= $estatClass ?>">
                                        <?= htmlspecialchars($c['estat']) ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="index.php?action=detall-comanda&id=<?= $c['id'] ?>" class="btn-detall-comanda">Veure Detall</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    
    <?php require __DIR__ . '/partials/cart-sidebar.php'; ?>
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
