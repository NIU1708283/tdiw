<?php
// Controller: Gestió de perfil d'usuari

require_once __DIR__ . '/../models/connectaBD.php';
require_once __DIR__ . '/../models/registrausuari.php';
require_once __DIR__ . '/../models/actualitzausuari.php';
require_once __DIR__ . '/../models/guardaComanda.php';

class PerfilController {
    
    public function registrarse() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=registre');
            exit;
        }
        
        $nom = $_POST['nom'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $adreca = $_POST['adreca'] ?? '';
        $poblacio = $_POST['poblacio'] ?? '';
        $codi_postal = $_POST['codi_postal'] ?? '';
        
        $connexio = getConnection();
        $result = registra_usuari($connexio, $nom, $email, $password, $adreca, $poblacio, $codi_postal);
        closeConnection($connexio);
        
        if (!$result['ok']) {
            $_SESSION['errors'] = $result['errors'];
            header('Location: index.php?action=registre');
            exit;
        }
        
        // Iniciar sessió automàticament
        $_SESSION['usuari'] = [
            'id' => $result['id'],
            'nom' => $result['nom'],
            'email' => $result['email']
        ];
        
        header('Location: index.php?action=home');
        exit;
    }
    
    public function iniciarSessio() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $connexio = getConnection();
        $result = verifica_usuari($connexio, $email, $password);
        closeConnection($connexio);
        
        if (!$result['ok']) {
            $_SESSION['errors'] = $result['errors'];
            header('Location: index.php?action=login');
            exit;
        }
        
        // Crear sessió
        $_SESSION['usuari'] = [
            'id' => $result['id'],
            'nom' => $result['nom'],
            'email' => $result['email']
        ];
        
        header('Location: index.php?action=home');
        exit;
    }
    
    public function logout() {
        // AJAX logout
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            unset($_SESSION['usuari']);
            echo json_encode(['ok' => true]);
            exit;
        }
        
        // Logout normal
        unset($_SESSION['usuari']);
        header('Location: index.php?action=home');
        exit;
    }
    
    public function actualitzarPerfil() {
        if (!isset($_SESSION['usuari'])) {
            header('Location: index.php?action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=editar_perfil');
            exit;
        }
        
        $id = $_SESSION['usuari']['id'];
        $nom = $_POST['nom'] ?? '';
        $email = $_POST['email'] ?? '';
        $adreca = $_POST['adreca'] ?? '';
        $poblacio = $_POST['poblacio'] ?? '';
        $codi_postal = $_POST['codi_postal'] ?? '';
        $password = $_POST['password'] ?? null;
        $foto_perfil = null;
        
        // Processar foto de perfil
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto_perfil'];
            
            // Validar MIME type
            $mime_type = mime_content_type($file['tmp_name']);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($mime_type, $allowed_mimes)) {
                $_SESSION['error'] = 'Tipus de fitxer no permès (només JPG, PNG, GIF)';
                header('Location: index.php?action=editar_perfil');
                exit;
            }
            
            // Validar mida (màxim 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = 'Fitxer massa gran (màxim 5MB)';
                header('Location: index.php?action=editar_perfil');
                exit;
            }
            
            // Generar nom segur
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $nom_fitxer = time() . '_' . uniqid() . '.' . $extension;
            $ruta_destino = __DIR__ . '/../uploadedFiles/' . $nom_fitxer;
            
            // Moure fitxer
            if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
                $foto_perfil = $nom_fitxer; // Guardar només el nom, no la ruta completa
            } else {
                $_SESSION['error'] = 'Error al pujar fitxer';
                header('Location: index.php?action=editar_perfil');
                exit;
            }
        }
        
        // Actualitzar usuari
        $connexio = getConnection();
        $result = actualitzarUsuari($connexio, $id, $nom, $email, $adreca, $poblacio, $codi_postal, $password, $foto_perfil);
        closeConnection($connexio);
        
        if (!$result['ok']) {
            $_SESSION['errors'] = $result['errors'];
            header('Location: index.php?action=editar_perfil');
            exit;
        }
        
        // Actualizar sessió
        $_SESSION['usuari']['nom'] = $nom;
        $_SESSION['usuari']['email'] = $email;
        
        $_SESSION['success'] = 'Perfil actualitzat correctament';
        header('Location: index.php?action=perfil');
        exit;
    }
}
?>
