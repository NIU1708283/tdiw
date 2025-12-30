<?php
declare(strict_types=1);
// /controllers/PerfilController.php

class PerfilController
{
    public function index(): void
    {
        require __DIR__ . '/../views/perfil.php';
    }

    public function actualitzarPerfil(): void
    {
        // Aquí aniria la lògica per actualitzar el perfil de l'usuari
        // Comprovar dades, pujar imatge, guardar a la base de dades, etc.

        // Després de processar, redirigir amb un missatge d'èxit
        header('Location: index.php?action=perfil&missatge_exit=ok');
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
