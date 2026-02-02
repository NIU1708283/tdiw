<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtenir ID de comanda de la URL
$idComanda = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idComanda === 0) {
    header('Location: index.php?action=home');
    exit;
}

// Carregar detalls de la comanda
require_once __DIR__ . '/../models/consultaDetallComanda.php';
$comanda = obtenir_detall_comanda($idComanda);

if (!$comanda) {
    header('Location: index.php?action=home&error=comanda_no_trovada');
    exit;
}

// Verificar que la comanda pertany a l'usuari loguejat
require_once __DIR__ . '/../models/connectaBD.php';
$conn = connectaBD();
$res = pg_query_params($conn, 'SELECT id_usuari FROM comanda WHERE id = $1', array($idComanda));
$row = pg_fetch_assoc($res);
pg_close($conn);

if (!$row || (isset($_SESSION['usuari']) && (int)$row['id_usuari'] !== (int)$_SESSION['usuari']['id'])) {
    header('Location: index.php?action=home&error=unauthorized');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmació de Comanda - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=10.0">
    <script src="script.js?v=26.0" defer></script>
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    
    <div><hr></div>
    
    <main>
        <section class="confirmacio-container" style="max-width: 1000px; margin: 30px auto; padding: 30px; background: #f9f9f9; border-radius: 8px;">
            <h1 style="text-align: center; color: #ff4500; margin-bottom: 30px;">✓ Comanda Confirmada</h1>
            
            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #4CAF50;">
                <h2 style="color: #4CAF50; margin-top: 0;">Gràcies per la teva compra!</h2>
                <p>La teva comanda ha estat guardada correctament. Rebràs un correu de confirmació en breus moments.</p>
            </div>
            
            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3>Detalls de la Comanda</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <p><strong>ID Comanda:</strong> <?= htmlspecialchars($comanda['id']) ?></p>
                        <p><strong>Data:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($comanda['data_comanda']))) ?></p>
                        <p><strong>Estat:</strong> <span style="background: #fff3cd; padding: 5px 10px; border-radius: 4px; color: #856404;"><?= htmlspecialchars($comanda['estat']) ?></span></p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 24px; color: #ff4500; margin: 0;"><strong>Total: <?= number_format((float)($comanda['preuTotal'] ?? $comanda['preutotal'] ?? 0), 2, ',', '.') ?>€</strong></p>
                    </div>
                </div>
            </div>
            
            <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3>Productes</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f0f0f0; border-bottom: 2px solid #ddd;">
                            <th style="padding: 10px; text-align: left;">Producte</th>
                            <th style="padding: 10px; text-align: center;">Quantitat</th>
                            <th style="padding: 10px; text-align: right;">Preu Unitari</th>
                            <th style="padding: 10px; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comanda['linies'] as $linia): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <?php if (!empty($linia['imatge'])): ?>
                                    <img src="<?= htmlspecialchars($linia['imatge']) ?>" alt="<?= htmlspecialchars($linia['nom']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($linia['nom']) ?></span>
                                </div>
                            </td>
                            <td style="padding: 12px; text-align: center;"><?= htmlspecialchars($linia['unitats']) ?></td>
                            <td style="padding: 12px; text-align: right;"><?= number_format((float)$linia['preu'], 2, ',', '.') ?>€</td>
                            <td style="padding: 12px; text-align: right; font-weight: bold;"><?= number_format((float)$linia['subtotal'], 2, ',', '.') ?>€</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                <a href="index.php?action=perfil" class="btn-primary" style="padding: 12px 30px; background: #ff4500; color: white; text-decoration: none; border-radius: 6px; display: inline-block; margin-right: 10px;">Tornar al Perfil</a>
                <a href="index.php?action=botiga" class="btn-secondary" style="padding: 12px 30px; background: #666; color: white; text-decoration: none; border-radius: 6px; display: inline-block;">Continuar Comprant</a>
            </div>
        </section>
    </main>
    
    <?php require __DIR__ . '/partials/cart-sidebar.php'; ?>
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>