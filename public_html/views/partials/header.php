<!-- PART DEL HEADER COMÚ A TOTES LES PÀGINES -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// DETERMINAR SI L'USUARI ESTÀ LOGUEJAT
if (isset($usuari_loguejat)) {
    $usuari_loguejat = (bool)$usuari_loguejat;
} elseif (array_key_exists('usuari', $_SESSION)) {
    $usuari_loguejat = !empty($_SESSION['usuari']);
} elseif (!empty($_COOKIE['logged_out'])) {
    $usuari_loguejat = false;
} else {
    $usuari_loguejat = false;
}

// NETEJAR COOKIE DE LOGOUT SI L'USUARI ESTÀ LOGUEJAT
if (!empty($_SESSION['usuari'])) {
    if (!empty($_COOKIE['logged_out'])) {
        setcookie('logged_out', '', time() - 3600, '/'); // LA COOKIE EXPIRA DESPRÉS D'UNA HORA
        unset($_COOKIE['logged_out']);
    }
}
?>
<!-- ENLLAÇOS A FULLS D'ESTIL -->
<link rel="stylesheet" href="style.css?v=10.0">

<header>
    <h1 id="title-logo">
        <a href="index.php">
            <img src="images/logo2.png" alt="ToonTunes Logo">
            ToonTunes
        </a>
    </h1>

    <nav id="nav-home">
        <ul>
            <li><a href="index.php?action=home">Inici</a></li>
            <li><a href="index.php?action=botiga">Botiga</a></li>
            <li><a href="index.php?action=contacte">Contacte</a></li>
            <!-- MENÚ DESPLEGABLE D'USUARI -->
            <li class="user-dropdown">
                <a href="#" class="dropdown-toggle" id="userMenuBtn">
                    <?php echo $usuari_loguejat ? ' El meu Compte' : ' Compte'; ?> 
                </a>

                <div id="userDropdown" class="dropdown-menu">
                    <?php if ($usuari_loguejat): ?>
                        <a href="index.php?action=perfil"> El meu Perfil</a>
                        <a href="index.php?action=comandes"> Les meves comandes</a>
                        <hr class="dropdown-divider">
                        <a href="index.php?action=logout" id="logout-link" style="color: #dc3545;"> Tancar Sessió</a>
                    <?php else: ?>
                        <a href="index.php?action=iniciarsesio" id="login-popup-link"> Iniciar Sessió</a>
                        <a href="index.php?action=register"> Registrar-se</a>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
        
        <button id="dark-toggle" class="dark-toggle" aria-pressed="false" title="Mode fosc" type="button">🌙</button>
    </nav>
</header>

<script>
    // LOGOUT VIA AJAX
    (function(){
        var logoutLink = document.getElementById('logout-link');
        if (!logoutLink) return;

        logoutLink.addEventListener('click', function(e){
            e.preventDefault();
            fetch('index.php?action=logout', { 
                method: 'POST', 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            })
            .then(resp => resp.json())
            .then(data => {
                if (data && data.ok) window.location.reload(); 
                else window.location.href = 'index.php?action=logout';
            })
            .catch(() => window.location.href = 'index.php?action=logout');
        });
    })();

    // LOGIN MODAL (PANTALLA EMERGENT) AMB SUPORT PER MODE FOSC I ESTILS CORREGITS
    (function(){
        var loginLink = document.getElementById('login-popup-link');
        if (!loginLink) return;

        // ESTILS PER AL MODAL INCORPORATS DINÀMICAMENT
        if (!document.getElementById('embedded-modal-styles')) {
            var css = document.createElement('style');
            css.id = 'embedded-modal-styles';
            css.textContent = `
                .embedded-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; }
                .embedded-modal { background: #fff; max-width: 600px; width: 95%; max-height: 90vh; overflow: auto; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); padding: 0; position: relative; }
                .embedded-modal header { display: flex; justify-content: flex-end; padding: 15px; position: absolute; right: 0; top: 0; z-index: 10; }
                .embedded-modal .modal-body { padding: 0; }
                /* Botó de tancar millorat */
                .embedded-modal .modal-close { 
                    background: #ff4500; 
                    border: none; 
                    font-size: 24px; 
                    color: white; 
                    cursor: pointer; 
                    width: 32px; 
                    height: 32px; 
                    border-radius: 50%; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center; 
                    line-height: 1; 
                    padding: 0;
                    padding-top: 2px; /* AJUST VISUAL PER CENTRAR LA X VERTICALMENT */
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2); 
                    transition: transform 0.2s;
                }
                .embedded-modal .modal-close:hover { transform: scale(1.1); }
                
                /* REGLES PER AL MODE FOSC */
                html.dark .embedded-modal { background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); color: #e9e9e9; }
                html.dark .embedded-modal .modal-close { background: #ff4500; color: white; }
            `;
            document.head.appendChild(css);
        }

        function openEmbeddedModal(url) {
            var overlay = document.createElement('div');
            overlay.className = 'embedded-modal-overlay';
            var modal = document.createElement('div');
            modal.className = 'embedded-modal';
            
            modal.innerHTML = `
                <header><button class="modal-close" title="Tancar">&times;</button></header>
                <div class="modal-body"><p style="padding:20px; text-align:center;">Carregant...</p></div>
            `;
            
            overlay.appendChild(modal);
            document.body.appendChild(overlay);

            const close = () => overlay.remove();
            modal.querySelector('.modal-close').onclick = close;
            overlay.onclick = (e) => { if(e.target === overlay) close(); };

            var fetchUrl = url + (url.indexOf('?') === -1 ? '?embed=1' : '&embed=1');
            fetch(fetchUrl)
                .then(r => r.text())
                .then(html => {
                    const body = modal.querySelector('.modal-body');
                    body.innerHTML = html;
                    
                    const content = body.querySelector('.contingut-principal');
                    if(content) {
                        content.style.border = 'none';
                        content.style.boxShadow = 'none';
                        content.style.maxWidth = '100%';
                        content.style.padding = '40px';
                    }

                    const form = body.querySelector('form');
                    if(form) {
                        form.addEventListener('submit', function(e){
                            e.preventDefault();
                            const fd = new FormData(form);
                            fetch(form.action || fetchUrl, { method: 'POST', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} })
                                .then(r => r.json().catch(() => ({}))) 
                                .then(d => {
                                    if(d.ok) { close(); window.location.reload(); }
                                    else { window.location.reload(); }
                                });
                        });
                    }
                });
        }

        loginLink.addEventListener('click', function(e){
            e.preventDefault();
            openEmbeddedModal(loginLink.getAttribute('href'));
        });
    })();
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js?v=10.0" defer></script>