<?php
session_start();

// Router Principal - Gestiona totes les peticions
$action = $_GET['action'] ?? 'home';

switch ($action) {
    // HOME
    case 'home':
        require __DIR__ . '/views/home.php';
        break;
    
    // CATEGORIES I PRODUCTES
    case 'categoria':
        require __DIR__ . '/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->categoria();
        break;
    
    case 'producte':
        require __DIR__ . '/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->detallProducte();
        break;
    
    case 'buscar':
        require __DIR__ . '/controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->buscar();
        break;
    
    // CARRETÓ
    case 'cart_afegir':
        require __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->afegir();
        break;
    
    case 'cart_eliminar':
        require __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->eliminar();
        break;
    
    case 'cart_modificar':
        require __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->modificar();
        break;
    
    case 'cart_buidar':
        require __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->buidar();
        break;
    
    case 'carretó':
        require __DIR__ . '/views/cart.php';
        break;
    
    case 'finalitzar_compra':
        require __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->finalitzarCompra();
        break;
    
    case 'confirmacio_comanda':
        require __DIR__ . '/views/confirmacio_comanda.php';
        break;
    
    // USUARI
    case 'registre':
        require __DIR__ . '/views/register.php';
        break;
    
    case 'registrar':
        require __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->registrarse();
        break;
    
    case 'login':
        require __DIR__ . '/views/iniciarsesio.php';
        break;
    
    case 'iniciar_sessio':
        require __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->iniciarSessio();
        break;
    
    case 'logout':
        require __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->logout();
        break;
    
    case 'perfil':
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require __DIR__ . '/views/perfil.php';
        break;
    
    case 'editar_perfil':
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require __DIR__ . '/views/editar-perfil.php';
        break;
    
    case 'actualitzar_perfil':
        require __DIR__ . '/controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->actualitzarPerfil();
        break;
    
    case 'historial':
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=login');
            exit;
        }
        require __DIR__ . '/views/historialComandes.php';
        break;
    
    default:
        require __DIR__ . '/views/home.php';
        break;
}
?>
