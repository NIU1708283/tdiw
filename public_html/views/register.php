<?php
// register.php - muestra el formulario de registro (GET) y procesa el registro (POST)
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once __DIR__ . '/../models/connectaBD.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nom = filter_var(trim($_POST['nom'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirm = $_POST['confirm_password'] ?? '';
	$adreca = filter_var(trim($_POST['adreca'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
	$poblacio = filter_var(trim($_POST['poblacio'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
    $codi_postal = trim($_POST['codi_postal'] ?? '');

	if ($nom === '' || $email === '' || $password === '' || $confirm === '') {
		$errors[] = 'Cal omplir tots els camps obligatoris.';
	}

	if ($password !== $confirm || strlen($password) < 6) {
		$errors[] = 'Les contrasenyes han de coincidir i tenir almenys 6 caràcters.';
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Correu electrònic no vàlid.';
	}

	if ($codi_postal !== '' && !preg_match('/^\d{5}$/', $codi_postal)) {
        $errors[] = 'El codi postal ha de tenir exactament 5 dígits.';
    }

    if (strlen($adreca) > 30) {
        $errors[] = 'L\'adreça no pot superar 30 caràcters.';
    }

    if (strlen($poblacio) > 30) {
        $errors[] = 'La població no pot superar 30 caràcters.';
    }

	if (empty($errors)) {
		require_once __DIR__ . '/../models/registrausuari.php';
		$res = registra_usuari($nom, $email, $password, $adreca, $poblacio, $codi_postal);
		if (isset($res['ok']) && $res['ok'] === true) {
			// iniciar sesión automáticamente con los datos devueltos
			if (!empty($res['id'])) {
				$_SESSION['usuari'] = ['id' => (int)$res['id'], 'nom' => ($res['nom'] ?? $nom), 'email' => ($res['email'] ?? $email)];
			} else {
				$_SESSION['usuari'] = ['nom' => $nom, 'email' => $email];
			}
			if (!empty($_COOKIE['logged_out'])) {
				setcookie('logged_out', '', time() - 3600, '/');
				unset($_COOKIE['logged_out']);
			}
			header('Location: index.php?action=perfil&regstatus=ok');
			exit;
		} else {
			// merge errors from model
			if (!empty($res['errors']) && is_array($res['errors'])) {
				$errors = array_merge($errors, $res['errors']);
			} else {
				$errors[] = 'S\'ha produït un error al registrar.';
			}
		}
	}
}

// Si no es POST (GET) o hay errores en POST, mostramos el formulario
?><!DOCTYPE html>
<html lang="ca">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registrar - ToonTunes</title>
	<link rel="stylesheet" href="style.css?v=10.0">
	<script src="script.js?v=26.0" defer></script>
</head>
<body>
	<?php require __DIR__ . '/partials/header.php'; ?>
	<div>
		<hr>
	</div>
	<main>
		<section class="container-content">
			<article class="contingut-principal">
				<h2>Registrar-se</h2>
				<?php if (!empty($errors)): ?>
					<div class="errors">
						<ul>
						<?php foreach($errors as $err): ?>
							<li><?= htmlspecialchars($err) ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<form method="post" action="index.php?action=register">
					<div class="form-field">
						<label for="nom">Nom complet</label>
					<input id="nom" name="nom" type="text" minlength="2" maxlength="100" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" title="Entre 2 i 100 caràcters">
				</div>

				<div class="form-field">
					<label for="email">Correu electrònic</label>
					<input id="email" name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
				</div>

				<div class="form-field">
					<label for="password">Contrasenya</label>
					<input id="password" name="password" type="password" minlength="6" maxlength="128" required title="Mínim 6 caràcters">
				</div>

				<div class="form-field">
					<label for="confirm_password">Confirmar contrasenya</label>
					<input id="confirm_password" name="confirm_password" type="password" minlength="6" maxlength="128" required title="Ha de coincidir amb la contrasenya anterior">
						<label for="adreca">Adreça (opcional)</label>
						<input id="adreca" name="adreca" type="text" maxlength="30" value="<?= htmlspecialchars($_POST['adreca'] ?? '') ?>">
					</div>

					<div class="form-field">
						<label for="poblacio">Població (opcional)</label>
						<input id="poblacio" name="poblacio" type="text" maxlength="30" value="<?= htmlspecialchars($_POST['poblacio'] ?? '') ?>">
					</div>

					<div class="form-field">
						<label for="codi_postal">Codi Postal (5 dígits)</label>
						<input id="codi_postal" name="codi_postal" pattern="\d{5}" type="text" value="<?= htmlspecialchars($_POST['codi_postal'] ?? '') ?>">
					</div>

					<div class="form-actions">
						<button type="submit">Registrar</button>
					</div>
				</form>
			</article>
		</section>
	</main>
	<?php require __DIR__ . '/partials/cart-sidebar.php'; ?>
	<?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>

