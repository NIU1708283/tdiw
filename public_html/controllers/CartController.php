<?php
declare(strict_types=1);
// /controllers/CartController.php

class CartController
{
    public function index(): void
    {
        // For now show a simple cart view
        require __DIR__ . '/../views/cart.php';
    }
    
    // Añadir producto al carrito
    public function afegir() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
        $preu = isset($_POST['preu']) ? floatval($_POST['preu']) : 0;
        $imatge = isset($_POST['imatge']) ? $_POST['imatge'] : '';
        $quantitat = isset($_POST['quantitat']) ? intval($_POST['quantitat']) : 1;
        
        if ($id > 0) {
            // Buscar si ya existe
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    $item['quantitat'] += $quantitat;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $id,
                    'nom' => $nom,
                    'preu' => $preu,
                    'imatge' => $imatge,
                    'quantitat' => $quantitat
                ];
            }
        }
        
        // Devolver el carrito actualizado como JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'count' => array_sum(array_column($_SESSION['cart'], 'quantitat'))
        ]);
        exit;
    }
    
    // Eliminar producto del carrito
    public function eliminar() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $index = isset($_POST['index']) ? intval($_POST['index']) : -1;
        
        if ($index >= 0 && isset($_SESSION['cart'][$index])) {
            array_splice($_SESSION['cart'], $index, 1);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexar
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'count' => array_sum(array_column($_SESSION['cart'], 'quantitat'))
        ]);
        exit;
    }
    
    // Obtener carrito actual
    public function obtenir() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'count' => isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantitat')) : 0
        ]);
        exit;
    }
    
    // Vaciar carrito (finalizar compra)
    public function buidar() {
        $_SESSION['cart'] = [];
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'cart' => [],
            'count' => 0
        ]);
        exit;
    }

    public function confirmacio(): void {
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=iniciarsesio');
            exit();
        }
        // Necessitarem les dades de l'usuari actualitzades per a l'adreça d'enviament
        require_once __DIR__ . '/../models/connectaBD.php';
        $conn = connectaBD();
        $res = pg_query_params($conn, "SELECT * FROM usuari WHERE id = $1", array($_SESSION['usuari']['id']));
        $dadesEnviament = pg_fetch_assoc($res);
    
        require __DIR__ . '/../views/confirmacio_comanda.php';
    }

    // Processa la inserció a la BD i buida el cabàs
    public function finalitzarCompra(): void {
        $productes = $_SESSION['cart'] ?? [];
        $idUsuari = $_SESSION['usuari']['id'];
        $total = array_sum(array_map(function($item) { return $item['preu'] * $item['quantitat']; }, $productes));
        
        require_once __DIR__ . '/../models/guardaComanda.php';
        if (guardar_comanda($idUsuari, $total, $productes)) {
            $_SESSION['cart'] = []; // Buidem el cabàs de la sessió
            header('Location: index.php?action=perfil&compra=ok');
        } else {
            header('Location: index.php?action=cart&error=db');
        }
        exit();
    }
}
?>
