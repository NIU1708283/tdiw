<?php
// Vista: Historial de comandes

require __DIR__ . '/partials/header.php';

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/guardaComanda.php';

if (!isset($_SESSION['usuari'])) {
    header('Location: index.php?action=login');
    exit;
}

$connexio = getConnection();
$comandes = obtenerComandesUsuari($connexio, $_SESSION['usuari']['id']);
closeConnection($connexio);
?>

<div class="historial-container">
    <h1>Les meves comandes</h1>
    
    <?php if (!empty($comandes)): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Número de comanda</th>
                    <th>Data</th>
                    <th>Total</th>
                    <th>Estat</th>
                    <th>Acció</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comandes as $comanda): ?>
                    <tr>
                        <td>#<?= $comanda['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($comanda['data_comanda'])) ?></td>
                        <td><?= $comanda['total'] ?>€</td>
                        <td><?= htmlspecialchars($comanda['estat']) ?></td>
                        <td>
                            <a href="index.php?action=confirmacio_comanda&id=<?= $comanda['id'] ?>" class="btn btn-small">Veure detalls</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tens cap comanda realitzada.</p>
        <a href="index.php?action=home" class="btn btn-primary">Começar a comprar</a>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
