<?php
// iniciarsesio.php - Solo inicio de sesión. Si no existe, sugiere registro.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/guardaCabas.php';

$errors = [];
$values = ['email'=>''];

// Detect embed or AJAX requests
$isEmbed = isset($_GET['embed']) && $_GET['embed'] === '1';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger campos mínimos: email y password
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $values['email'] = $email;

    // Validaciones básicas
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correu electrònic no vàlid.';
    if ($password === '' || strlen($password) < 6) $errors[] = 'La contrasenya ha de tenir almenys 6 caràcters.';

    if (empty($errors)) {
        require_once __DIR__ . '/../models/registrausuari.php';
        $resultado = verifica_usuari($email, $password);
        
        if ($resultado['ok']) {
            // Login correcte: guardem dades a la sessió
            $_SESSION['usuari'] = $resultado['usuari'];
            
            // Carregar el carrito guardado de la base de datos
            $cart_guardado = carregar_cabas_usuari((int)$resultado['usuari']['id']);
            if (!empty($cart_guardado)) {
                // Si ya hay productos en el carrito de sesión, combinar
                $_SESSION['cart'] = array_merge($_SESSION['cart'] ?? [], $cart_guardado);
            }
            
            // Eliminar cookie de logged_out si existeix
            if (!empty($_COOKIE['logged_out'])) {
                setcookie('logged_out', '', time() - 3600, '/');
                unset($_COOKIE['logged_out']);
            }
            
            // Resposta per a AJAX
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => true]);
                exit;
            }
            
            // Redirecció per a navegació normal
            header('Location: index.php?action=perfil');
            exit;
        } else {
            // Login fallit: mostrar errors
            $errors = array_merge($errors, $resultado['errors'] ?? ['Error desconegut.']);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Iniciar Sessió - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=10.0">
    <script src="script.js?v=26.0" defer></script>
</head>
<body>
<?php
// Output: if embed requested, return only the form fragment (no header/footer)
if ($isEmbed) {
    // Embedded fragment (Popup)
    ?>
    <section class="container-content" style="box-shadow: none; padding: 0;">
        <article class="contingut-principal" style="border: none; box-shadow: none; max-width: 100%;">
            <h2 style="text-align: center;">Iniciar Sessió</h2>
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                    <?php foreach($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="iniciarsesio-form" method="post" action="/index.php?action=iniciarsesio">
                <label for="email">Correu electrònic</label>
                <input id="email" name="email" type="email" value="<?= htmlspecialchars($values['email']) ?>" required>

                <label for="password">Contrasenya</label>
                <input id="password" name="password" type="password" required>

                <div style="margin-top:1rem;">
                    <button type="submit" style="width: 100%;">Iniciar Sessió</button>
                </div>

                <div style="margin-top: 1.5rem; text-align: center; font-size: 0.95rem; padding-top: 1rem; border-top: 1px solid #eee;">
                    <p>No tens un compte? <a href="index.php?action=register" style="font-weight: bold;">Registra't aquí</a></p>
                </div>
            </form>
        </article>
    </section>
    <?php
    exit;
}

// Full page output
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<main>
    <section class="container-content">
        <article class="contingut-principal">
            <h2>Iniciar Sessió</h2>
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                    <?php foreach($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form id="iniciarsesio-form" method="post" action="/index.php?action=iniciarsesio">
                <label for="email">Correu electrònic</label>
                <input id="email" name="email" type="email" value="<?= htmlspecialchars($values['email']) ?>" required>

                <label for="password">Contrasenya</label>
                <input id="password" name="password" type="password" required>

                <div style="margin-top:1rem;">
                    <button type="submit" style="width: 100%;">Iniciar Sessió</button>
                </div>

                <div style="margin-top: 1.5rem; text-align: center; font-size: 0.95rem; padding-top: 1rem; border-top: 1px solid #eee;">
                    <p>No tens un compte? <a href="index.php?action=register" style="font-weight: bold;">Registra't aquí</a></p>
                </div>
            </form>
        </article>
    </section>
</main>
<?php require __DIR__ . '/partials/cart-sidebar.php'; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>