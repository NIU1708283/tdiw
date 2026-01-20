<?php
function connectaBD() {
    $servidor = "deic-docencia.uab.cat";
    $port = "5432";
    $DBnom = "tdiw-i3";
    $usuari = "tdiw-i3";
    $clau = "u1Kq28Lt";
    
    $connexio = pg_connect("host=$servidor port=$port dbname=$DBnom user=$usuari password=$clau");
    
    if (!$connexio) {
        throw new Exception("(pg_connect) " . pg_last_error());
    }
    
    return $connexio;
}
?>
