<?php
declare(strict_types=1);
// /controllers/ContacteController.php
// Mostra la pàgina de contacte

class ContacteController
{
    public function index(): void
    {
        require __DIR__ . '/../views/contacte.php';
    }
}
?>