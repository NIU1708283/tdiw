<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToonTunes - La botiga d'instruments més virtuosa</title>
    <link rel="stylesheet" href="style.css?v=22.0">
    <script src="script.js?v=16.0" defer></script>
</head>

<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    <div>
        <hr>
    </div>
    
    <main class="home-main">
        <?php if (!empty($_GET['message'])): ?>
        <div class="logout-message">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1 class="hero-title">Troba la teva guitarra perfecta</h1>
                <p class="hero-subtitle">Des de clàssiques fins elèctriques. La música comença aquí.</p>
                <a href="index.php?action=botiga" class="hero-cta">Explorar catàleg →</a>
            </div>
        </section>

        <!-- Categories Preview -->
        <section class="home-categories">
            <h2 class="section-title">Les nostres col·leccions</h2>
            <div class="categories-grid-home">
                <div class="category-card-home electrics">
                    <div class="category-overlay"></div>
                    <h3>Guitarres Elèctriques</h3>
                    <p>Potència i versatilitat</p>
                </div>
                <div class="category-card-home acoustics">
                    <div class="category-overlay"></div>
                    <h3>Guitarres Acústiques</h3>
                    <p>So natural i càlid</p>
                </div>
                <div class="category-card-home classics">
                    <div class="category-overlay"></div>
                    <h3>Guitarres Clàssiques</h3>
                    <p>Tradició i elegància</p>
                </div>
                <div class="category-card-home bass">
                    <div class="category-overlay"></div>
                    <h3>Baixos Elèctrics</h3>
                    <p>La base del ritme</p>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section">
            <div class="about-content">
                <div class="about-text">
                    <h2>Per què ToonTunes?</h2>
                    <p>Som una botiga especialitzada en guitarres amb més de 20 anys d'experiència. La nostra passió és connectar músics amb l'instrument perfecte.</p>
                    <p>Des de principiants que busquen la seva primera guitarra fins a professionals que necessiten un instrument d'alta gamma, a ToonTunes trobaràs el que necessites.</p>
                    <a href="index.php?action=contacte" class="btn-secondary">Contacta'ns</a>
                </div>
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=600&h=400&fit=crop" alt="Taller de guitarres">
                </div>
            </div>
        </section>
    </main>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>