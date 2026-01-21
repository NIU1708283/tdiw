<?php
// Controlador de Productos - Patrón MVC Básico
// Incluir modelos necesarios
include_once __DIR__ . '/../models/connectaBD.php';
include_once __DIR__ . '/../models/consultaCategories.php';
include_once __DIR__ . '/../models/consultaProductes.php';

class ProductoController {

    // Vista principal de la botiga (muestra categorías)
    public function botiga() {
        try {
            // Conectar a la base de datos
            $connexio = connectaBD();
            
            // Obtener categorías
            $resultat_categories = consultaCategories($connexio);
            
            // Cerrar conexión
            pg_close($connexio);
            
            // Cargar vista
            include __DIR__ . '/../views/llistatCategories.php';
            
        } catch(Exception $e) {
            echo "<!DOCTYPE html>
            <html lang='ca'>
            <head>
                <meta charset='UTF-8'>
                <title>Error - ToonTunes</title>
                <link rel='stylesheet' href='style.css'>
            </head>
            <body>";
            include __DIR__ . '/../views/partials/header.php';
            echo "<main style='padding: 40px 20px;'>
                <div style='background: #fee; border: 2px solid #c33; padding: 20px; border-radius: 8px; max-width: 800px; margin: 0 auto;'>
                    <h2 style='color: #c33;'>⚠️ Error de connexió</h2>
                    <p>" . htmlspecialchars($e->getMessage()) . "</p>
                </div>
            </main>
            </body>
            </html>";
        }
    }

    public function categoria() {
        $categoriaNom = isset($_GET['cat']) ? $_GET['cat'] : null;
        
        if (!$categoriaNom) {
            header('Location: index.php?action=botiga');
            exit;
        }
        
        try {
            // Conectar a la base de datos
            $connexio = connectaBD();
            
            // Buscar productos de la categoría
            if (isset($_GET['q']) && !empty($_GET['q'])) {
                $cerca = $_GET['q'];
                $resultat_productes = cercaProductesEnCategoria($connexio, $categoriaNom, $cerca);
            } else {
                $resultat_productes = consultaProductesPerCategoria($connexio, $categoriaNom);
            }
            
            // Cerrar conexión
            pg_close($connexio);
            
            // Detectar si es petición AJAX
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                // Respuesta JSON para AJAX
                header('Content-Type: application/json; charset=utf-8');
                $response = [
                    'success' => true,
                    'category' => $categoriaNom,
                    'products' => is_array($resultat_productes) ? $resultat_productes : []
                ];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            // Respuesta HTML tradicional (fallback) - redirigir a botiga ya que no hay vista
            header('Location: index.php?action=botiga');
            exit;
            
        } catch(Exception $e) {
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            header('Location: index.php?action=botiga');
            exit;
        }
    }

    public function detallProducte() {
        $producteId = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        if (!$producteId) {
            header('Location: index.php?action=botiga');
            exit;
        }
        
        try {
            $connexio = connectaBD();
            $producte = consultaProductePerID($connexio, $producteId);
            
            if (!$producte) {
                pg_close($connexio);
                header('Location: index.php?action=botiga');
                exit;
            }
            
            pg_close($connexio);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'product' => $producte
            ]);
            exit;
            
        } catch(Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function buscar() {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        if (strlen($q) < 2) {
            header('Location: index.php?action=botiga');
            exit;
        }
        
        try {
            $connexio = connectaBD();
            require_once __DIR__ . '/../models/consultaProductes.php';
            $resultat_productes = cercaProductesGlobal($connexio, $q);
            pg_close($connexio);
            
            // Detectar si es petición AJAX
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'query' => $q,
                    'products' => $resultat_productes ?: []
                ]);
                exit;
            }
            
            // Para solicitud normal, mostrar vista
            require __DIR__ . '/../views/botiga.php';
            
        } catch(Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            } else {
                header('Location: index.php?action=botiga&error=search_error');
            }
            exit;
        }
    }
}
?>
