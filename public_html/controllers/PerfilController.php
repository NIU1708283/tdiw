<?php
declare(strict_types=1);
// /controllers/PerfilController.php// Perfil, registre, login i edició d'usuaris
class PerfilController
{
    public function index(): void
    {
        // Verifiquem si l'usuari està loguejat (Sessió 5/4.4)
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=iniciarsesio');
            exit();
        }

        // Passem la variable a la vista
        require __DIR__ . '/../views/perfil.php';
    }

    public function editarPerfil(): void
    {
        // Verifiquem si l'usuari està loguejat
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=iniciarsesio');
            exit();
        }

        // Passem la variable a la vista
        require __DIR__ . '/../views/editar-perfil.php';
    }

    public function historialComandes(): void
    {
        // Verifiquem si l'usuari està loguejat
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=iniciarsesio');
            exit();
        }

        $idUsuari = (int)$_SESSION['usuari']['id'];
        
        // Carreguem el model de comandes
        require_once __DIR__ . '/../models/consultaComandes.php';
        $llistatComandes = obtenir_comandes_usuari($idUsuari);

        // Passem la variable a la vista
        require __DIR__ . '/../views/historialComandes.php';
    }

    public function actualitzarPerfil(): void
    {
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=iniciarsesio');
            exit();
        }

        $idUsuari = $_SESSION['usuari']['id'];
        
        // Validar i sanititzar les dades
        $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $adreca = filter_var($_POST['adreca'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $poblacio = filter_var($_POST['poblacio'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $codiPostal = filter_var($_POST['codi_postal'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validar que almenys el nom estigui omplert
        if (empty($nom) || strlen($nom) < 2 || strlen($nom) > 100) {
            header('Location: index.php?action=perfil&error=nom_invalid');
            exit();
        }
        
        // Validar email si s'ha proporcionat
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?action=perfil&error=email_invalid');
            exit();
        }
        
        // Validar codi postal (5 dígits)
        if (!empty($codiPostal) && !preg_match('/^\d{5}$/', $codiPostal)) {
            header('Location: index.php?action=perfil&error=codi_postal_invalid');
            exit();
        }
        
        $rutaFoto = null;
        
        // Manejo de la pujada de fitxer d'imatge
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $fitxer = $_FILES['foto_perfil'];
            
            // Validar tipus de fitxer
            $tiposPermesos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fitxer['type'], $tiposPermesos, true)) {
                header('Location: index.php?action=perfil&error=tipo_archivo_no_permitido');
                exit();
            }
            
            // Validar mida (màxim 5MB)
            if ($fitxer['size'] > 5 * 1024 * 1024) {
                header('Location: index.php?action=perfil&error=archivo_muy_grande');
                exit();
            }
            
            // Crear directori si no existeix
            $uploadDir = __DIR__ . "/../uploadedFiles/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generar nom segur per al fitxer
            $extensio = pathinfo($fitxer['name'], PATHINFO_EXTENSION);
            $nomFitxer = time() . "_" . uniqid() . "." . $extensio;
            $rutaAbsoluta = $uploadDir . $nomFitxer;
            
            // Moure el fitxer
            if (!move_uploaded_file($fitxer['tmp_name'], $rutaAbsoluta)) {
                header('Location: index.php?action=perfil&error=upload_failed');
                exit();
            }
            
            $rutaFoto = $nomFitxer;
        }
        
        $passwordHashNova = null;
        
        // Manejo de canvi de contrasenya
        if (!empty($newPassword)) {
            // Si vol canviar la contrasenya, necessita verificar l'actual
            if (empty($oldPassword)) {
                header('Location: index.php?action=perfil&error=contrasenya_actual_requerida');
                exit();
            }
            
            // Validar les contrassenyes noves
            if ($newPassword !== $confirmPassword) {
                header('Location: index.php?action=perfil&error=contrassenyes_no_coincideixen');
                exit();
            }
            
            if (strlen($newPassword) < 6 || strlen($newPassword) > 128) {
                header('Location: index.php?action=perfil&error=contrasenya_length');
                exit();
            }
            
            // Verificar la contrasenya actual
            require_once __DIR__ . '/../models/registrausuari.php';
            $verifica = verifica_usuari($_SESSION['usuari']['email'], $oldPassword);
            
            if (!$verifica || !isset($verifica['ok']) || !$verifica['ok']) {
                header('Location: index.php?action=perfil&error=contrasenya_incorrecta');
                exit();
            }
            
            // Hash de la nova contrasenya
            $passwordHashNova = password_hash($newPassword, PASSWORD_BCRYPT);
        }
        
        // Actualitzar a la BD
        require_once __DIR__ . '/../models/actualitzausuari.php';
        $resultat = actualitza_dades_usuari(
            $idUsuari,
            $nom,
            !empty($email) ? $email : null,
            !empty($adreca) ? $adreca : null,
            !empty($poblacio) ? $poblacio : null,
            !empty($codiPostal) ? $codiPostal : null,
            $rutaFoto,
            $passwordHashNova
        );
        
        if ($resultat) {
            // Actualitzar dades de la sessió
            $_SESSION['usuari']['nom'] = $nom;
            if (!empty($email)) {
                $_SESSION['usuari']['email'] = $email;
            }
            if ($rutaFoto) {
                $_SESSION['usuari']['foto_perfil'] = $rutaFoto;
            }
            
            header('Location: index.php?action=perfil&missatge_exit=ok');
        } else {
            header('Location: index.php?action=perfil&error=bd_error');
        }
        exit();
    }

    public function iniciarSessio(): void
    {
        require __DIR__ . '/../views/iniciarsesio.php';
    }

    public function registrarse(): void
    {
        require __DIR__ . '/../views/register.php';
    }

    public function logout(): void
    {
        // Simple logout: destroy session and redirect to home
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Guardar el carrito antes de cerrar sesión
        if (isset($_SESSION['usuari']['id']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            require_once __DIR__ . '/../models/guardaCabas.php';
            guardar_cabas_usuari((int)$_SESSION['usuari']['id'], $_SESSION['cart']);
        }

        // Clear session data
        $_SESSION = [];

        // Remove session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        // Mark a short-lived cookie so server-side rendering can detect logged-out state
        setcookie('logged_out', '1', time() + 3600, '/');

        // If request is AJAX (fetch), return JSON response so header JS can handle UI update
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($isAjax || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'message' => 'sessió tancada correctament']);
            exit();
        }

        // Fallback: Redirect to home with a friendly message
        $msg = urlencode('sessió tancada correctament');
        header('Location: index.php?action=home&message=' . $msg);
        exit();
    }
}
?>
