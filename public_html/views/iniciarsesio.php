<?php
// iniciarsesio.php - Solo inicio de sesión. Si no existe, sugiere registro.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/connectaBD.php';

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
        try {
            $conn = connectaBD();

            // Buscar usuario por email
            $res = pg_query_params($conn, 'SELECT id, password_hash, nom FROM usuari WHERE email = $1', array($email));
            
            if ($res && pg_num_rows($res) > 0) {
                // Usuario existe -> intentar login
                $row = pg_fetch_assoc($res);
                if (!empty($row['password_hash']) && password_verify($password, $row['password_hash'])) {
                    // Login correcto
                    $_SESSION['usuari'] = ['id' => (int)$row['id'], 'nom' => $row['nom'] ?? $email, 'email' => $email];
                    
                    if (!empty($_COOKIE['logged_out'])) {
                        setcookie('logged_out', '', time() - 3600, '/');
                        unset($_COOKIE['logged_out']);
                    }
                    
                    if ($isAjax) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode(['ok' => true]);
                        exit;
                    }
                    
                    header('Location: index.php?action=perfil');
                    exit;
                } else {
                    $errors[] = 'Credencials incorrectes.';
                }
            } else {
                // Usuario NO existe -> NO registrar automáticamente, mostrar error
                $errors[] = 'Aquest usuari no existeix.';
            }

        } catch (Exception $e) {
            $errors[] = 'Error de connexió amb la base de dades.';
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
    <script src="script.js?v=10.0" defer></script>
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
<?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>