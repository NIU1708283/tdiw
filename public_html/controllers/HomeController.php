<?php
declare(strict_types=1);
class HomeController {
    
    public function index() {
        require_once __DIR__ . '/../views/home.php';
    }

    public function botiga() {
        require_once __DIR__ . '/../views/botiga.php';
    }

    public function perfil() {
        require_once __DIR__ . '/../views/perfil.php';
    }

    public function contacte() {
        require_once __DIR__ . '/../views/contacte.php';
    }
}
?>
