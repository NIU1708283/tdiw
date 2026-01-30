<?php
/**
 * Front Controller & Router - ToonTunes
 */
declare(strict_types=1); // Requisit Sessió 2

// 1. Configuració d'errors (Només per desenvolupament)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 2. Inici de Sessió Global
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Router - Dirigeix les peticions als controladors
$action = $_GET['action'] ?? 'home'; // Operador de fusió de null (més net que isset)

switch($action) {
    case 'home':
    case 'inicio':
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
    
    case 'botiga':
    case 'tienda':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->botiga();
        break;
    
    case 'categoria':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->categoria();
        break;

    case 'buscar':
    case 'search':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->buscar();
        break;
    
    case 'producto':
    case 'producte':
        require_once __DIR__ . '/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->detallProducte();
        break;
    
    case 'contacte':
    case 'contacto':
        require_once __DIR__ . '/controllers/ContacteController.php';
        $controller = new ContacteController();
        $controller->index();
        break;
    
    case 'perfil':
    case 'about':
        require_once __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->index();
        break;

    case 'editar-perfil':
    case 'editarperfil':
        require_once __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->editarPerfil();
        break;

    case 'iniciarsesio':
        require_once __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->iniciarSessio();
        break;

    case 'register':
        require_once __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->registrarse();
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->logout();
        break;

    // Carret de la compra
    case 'cistella':
    case 'cart':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->index();
        break;
    
    case 'cart_afegir':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->afegir();
        break;
    
    case 'cart_eliminar':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->eliminar();
        break;
    
    case 'cart_obtenir':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->obtenir();
        break;
    
    case 'cart_buidar':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->buidar();
        break;

    case 'cart_modificar':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->modificar();
        break;
    
    case 'finalitzar_compra':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->finalitzarCompra();
        break;

    case 'confirmacio-comanda':
    case 'confirmacio_comanda':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->confirmacio();
        break;

    case 'detall-comanda':
    case 'detall_comanda':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->detallComanda();
        break;

    case 'actualitzar-perfil':
    case 'actualitzarperfil':
        require_once __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->actualitzarPerfil();
        break;

    case 'historial-comandes':
    case 'historialcomandes':
        require_once __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->historialComandes();
        break;

    default:
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
?>