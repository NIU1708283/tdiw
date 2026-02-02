<?php
// Vista: Header/Navbar

$usuari_loguejat = isset($_SESSION['usuari']);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Botiga Online - ToonTunes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
<!-- NAVBAR -->
<header class="navbar">
    <div class="container">
        <div class="navbar-logo">
            <a href="index.php?action=home">🎵 BOTIGA</a>
        </div>
        
        <nav class="navbar-menu">
            <a href="index.php?action=home" class="nav-link">Home</a>
            <a href="index.php?action=home" class="nav-link">Productes</a>
            <a href="#" onclick="window.openCartGlobal()" class="nav-link">🛒 Carretó (<span id="cart-count">0</span>)</a>
            
            <?php if ($usuari_loguejat): ?>
                <div class="dropdown-menu">
                    <button class="nav-link dropdown-toggle">👤 <?= htmlspecialchars($_SESSION['usuari']['nom']) ?></button>
                    <div class="dropdown-content">
                        <a href="index.php?action=perfil">El meu perfil</a>
                        <a href="index.php?action=historial">Les meves comandes</a>
                        <a href="index.php?action=editar_perfil">Editar perfil</a>
                        <a href="#" onclick="logoutAJAX()">Tancar sessió</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="index.php?action=login" class="nav-link">Login</a>
                <a href="index.php?action=registre" class="nav-link btn-primary">Registre</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<!-- BUSCADOR -->
<div class="search-container">
    <input type="text" id="searchInput" placeholder="Buscar productes..." onkeyup="performGlobalSearch(this.value)">
    <div id="searchResults" class="search-results"></div>
</div>

<main class="container">
