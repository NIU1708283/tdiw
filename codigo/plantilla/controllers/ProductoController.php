<?php
// Controller: Gestió de productes i categories

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/consultaCategories.php';
require_once __DIR__ . '/../models/consultaProductes.php';

class ProductoController {
    
    public function categoria() {
        $connexio = getConnection();
        
        if (!isset($_GET['cat'])) {
            echo json_encode(['success' => false, 'error' => 'Categoria no especificada']);
            exit;
        }
        
        $categoria_id = intval($_GET['cat']);
        $query_search = $_GET['q'] ?? '';
        
        // Si hi ha cerca, fer búsqueda
        if (!empty($query_search)) {
            $productes = cercaProductesEnCategoria($connexio, $categoria_id, $query_search);
        } else {
            $productes = consultaProductesPerCategoria($connexio, $categoria_id);
        }
        
        closeConnection($connexio);
        
        // Si és AJAX, retornar JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode([
                'success' => true,
                'data' => $productes
            ]);
            exit;
        }
        
        // Si no és AJAX, mostrar vista
        include __DIR__ . '/../views/llistatCategories.php';
    }
    
    public function detallProducte() {
        $connexio = getConnection();
        
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'error' => 'Producte no especificat']);
            exit;
        }
        
        $producte_id = intval($_GET['id']);
        $producte = obtenerProductoById($connexio, $producte_id);
        
        closeConnection($connexio);
        
        if (!$producte) {
            echo json_encode(['success' => false, 'error' => 'Producte no trobat']);
            exit;
        }
        
        // Si és AJAX, retornar JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode([
                'success' => true,
                'data' => $producte
            ]);
            exit;
        }
    }
    
    public function buscar() {
        $connexio = getConnection();
        
        if (!isset($_GET['q'])) {
            echo json_encode(['success' => false, 'error' => 'Terme de búsqueda no especificat']);
            exit;
        }
        
        $query = $_GET['q'];
        $productes = cercaProductesGlobal($connexio, $query);
        
        closeConnection($connexio);
        
        echo json_encode([
            'success' => true,
            'data' => $productes
        ]);
        exit;
    }
}
?>
