<?php
declare(strict_types=1);
// /controllers/PerfilController.php

class PerfilController
{
    public function index(): void
    {
        // Verifiquem si l'usuari està loguejat (Sessió 5/4.4)
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=iniciarsesio');
        exit();
        }

        $idUsuari = (int)$_SESSION['usuari']['id'];
        // Carreguem el model de comandes
        require_once __DIR__ . '/../models/consultaComandes.php';
        $llistatComandes = obtenir_comandes_usuari($idUsuari);

        // Passem la variable a la vista
        require __DIR__ . '/../views/perfil.php';
    }

    public function actualitzarPerfil(): void
    {
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=iniciarsesio');
            exit();
        }

        $idUsuari = $_SESSION['usuari']['id'];
        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $rutaImatge = null;

        // Gestió del fitxer (Apèndix A.3)
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $nomFitxer = time() . "_" . $_FILES['avatar']['name'];
            $rutaDesti = __DIR__ . "/../public/img/avatars/" . $nomFitxer;
        
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $rutaDesti)) {
                $rutaImatge = "img/avatars/" . $nomFitxer;
            }
        }

        // Crida al model per actualitzar la BD (cal crear aquesta funció)
        require_once __DIR__ . '/../models/actualitzausuari.php';
        $res = actualitza_dades_usuari($idUsuari, $nom, $rutaImatge);

        if ($res) {
            $_SESSION['usuari']['nom'] = $nom;
            if ($rutaImatge) $_SESSION['usuari']['foto'] = $rutaImatge;
            header('Location: index.php?action=perfil&update=success');
        } else {
            header('Location: index.php?action=perfil&update=error');
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
