<?php
// Controller: Gestió del carretó

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/guardaComanda.php';

class CartController {
    
    public function afegir() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        // Validar que vengui per AJAX
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(400);
            exit;
        }
        
        // Obtenir dades
        $id = intval($_POST['id'] ?? 0);
        $nom = htmlspecialchars($_POST['nom'] ?? '');
        $preu = floatval($_POST['preu'] ?? 0);
        $imatge = htmlspecialchars($_POST['imatge'] ?? '');
        $quantitat = intval($_POST['quantitat'] ?? 1);
        
        if ($id <= 0 || $preu <= 0) {
            echo json_encode(['success' => false, 'error' => 'Dades invàlides']);
            exit;
        }
        
        // Inicialitzar carretó si no existeix
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Buscar si el producte ja està al carretó
        $encontrado = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                $item['quantitat'] += $quantitat;
                $encontrado = true;
                break;
            }
        }
        
        // Si no està, afegir-lo
        if (!$encontrado) {
            $_SESSION['cart'][] = [
                'id' => $id,
                'nom' => $nom,
                'preu' => $preu,
                'imatge' => $imatge,
                'quantitat' => $quantitat
            ];
        }
        
        // Calcular total
        $total = 0;
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['preu'] * $item['quantitat'];
            $count += $item['quantitat'];
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'total' => $total,
            'count' => $count
        ]);
        exit;
    }
    
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        $index = intval($_POST['index'] ?? -1);
        
        if ($index < 0 || !isset($_SESSION['cart'][$index])) {
            echo json_encode(['success' => false, 'error' => 'Item no trobat']);
            exit;
        }
        
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexar
        
        // Calcular total
        $total = 0;
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['preu'] * $item['quantitat'];
            $count += $item['quantitat'];
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'total' => $total,
            'count' => $count
        ]);
        exit;
    }
    
    public function modificar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        $index = intval($_POST['index'] ?? -1);
        $quantitat = intval($_POST['quantitat'] ?? 1);
        
        if ($index < 0 || !isset($_SESSION['cart'][$index]) || $quantitat < 1) {
            echo json_encode(['success' => false, 'error' => 'Dades invàlides']);
            exit;
        }
        
        $_SESSION['cart'][$index]['quantitat'] = $quantitat;
        
        // Calcular total
        $total = 0;
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['preu'] * $item['quantitat'];
            $count += $item['quantitat'];
        }
        
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'],
            'total' => $total,
            'count' => $count
        ]);
        exit;
    }
    
    public function buidar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        $_SESSION['cart'] = [];
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    public function finalitzarCompra() {
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if (empty($_SESSION['cart'])) {
            header('Location: index.php?action=home');
            exit;
        }
        
        // Calcular total
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['preu'] * $item['quantitat'];
        }
        
        // Guardar comanda
        $connexio = getConnection();
        $result = guardarComanda($connexio, $_SESSION['usuari']['id'], $_SESSION['cart'], $total);
        closeConnection($connexio);
        
        if (!$result['ok']) {
            $_SESSION['error'] = 'Error al guardar la comanda';
            header('Location: index.php?action=carretó');
            exit;
        }
        
        // Buidar carretó
        $_SESSION['cart'] = [];
        
        // Redirigir a confirmació
        header('Location: index.php?action=confirmacio_comanda&id=' . $result['comanda_id']);
        exit;
    }
}
?>
