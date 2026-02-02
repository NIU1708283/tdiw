<?php
// Vista: Confirmació de comanda

require __DIR__ . '/partials/header.php';

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/guardaComanda.php';

if (!isset($_GET['id'])) {
    header('Location: index.php?action=home');
    exit;
}

$comanda_id = intval($_GET['id']);
$connexio = getConnection();
$comanda = obtenerComanda($connexio, $comanda_id);
$linies = obtenerLiniesComanda($connexio, $comanda_id);
closeConnection($connexio);

if (!$comanda) {
    header('Location: index.php?action=home');
    exit;
}
?>

<div class="confirmation-container">
    <div class="confirmation-message">
        <h1>✅ Comanda confirmada!</h1>
        <p>Gràcies per la teva compra</p>
    </div>
    
    <div class="confirmation-details">
        <h3>Resum de la comanda</h3>
        
        <div class="order-info">
            <p><strong>Número de comanda:</strong> #<?= $comanda['id'] ?></p>
            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($comanda['data_comanda'])) ?></p>
            <p><strong>Client:</strong> <?= htmlspecialchars($comanda['nom']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($comanda['email']) ?></p>
        </div>
        
        <h4>Productes</h4>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Producte</th>
                    <th>Preu</th>
                    <th>Quantitat</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($linies as $linia): ?>
                    <tr>
                        <td><?= htmlspecialchars($linia['nom']) ?></td>
                        <td><?= $linia['preu_unitari'] ?>€</td>
                        <td><?= $linia['quantitat'] ?></td>
                        <td><?= $linia['preu_unitari'] * $linia['quantitat'] ?>€</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="order-total">
            <h3>Total: <?= $comanda['total'] ?>€</h3>
        </div>
        
        <div class="confirmation-actions">
            <a href="index.php?action=home" class="btn btn-primary">Continuar comprant</a>
            <a href="index.php?action=historial" class="btn btn-secondary">Veure totes les comandes</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
