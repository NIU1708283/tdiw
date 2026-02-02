<?php
// Connexió a la Base de Dades PostgreSQL

function getConnection() {
    $host = 'localhost';
    $dbname = 'tdiw_botiga';  // Canviar pel vostre nom BD
    $user = 'postgres';        // Canviar pel vostre usuari
    $password = 'password';    // Canviar per la vostra contrasenya
    $port = 5432;

    try {
        $conn = pg_connect(
            "host=$host port=$port dbname=$dbname user=$user password=$password"
        );
        
        if (!$conn) {
            throw new Exception("No s'ha pogut connectar a la BD");
        }
        
        return $conn;
    } catch (Exception $e) {
        die("Error de connexió: " . $e->getMessage());
    }
}

function closeConnection($conn) {
    if ($conn) {
        pg_close($conn);
    }
}
?>
