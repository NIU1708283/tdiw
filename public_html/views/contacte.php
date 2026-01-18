<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacte - ToonTunes</title>
    <link rel="stylesheet" href="style.css?v=10.0">
    <script src="script.js?v=10.0" defer></script>
</head>


<body>
    <?php require __DIR__ . '/partials/header.php'; ?>
    <div>
        <hr>
    </div>
    <main>
        <section class="container-content">
            <article class="contingut-principal">
                <h1>Contacta amb ToonTunes</h1>
                <p>Si tens qualsevol dubte, suggeriment o necessites assistència, 
                   no dubtis a posar-te en contacte amb nosaltres. Estem aquí per ajudar-te 
                   i assegurar-nos que la teva experiència amb ToonTunes sigui inoblidable.
                </p>
                <h2>Formulari de Contacte</h2>
                <form action="index.php?action=contacte" method="post">
                    <label for="name">Nom:</label><br>
                    <input type="text" id="name" name="name" required><br><br>
                    
                    <label for="email">Correu Electrònic:</label><br>
                    <input type="email" id="email" name="email" required><br><br>
                    
                    <label for="message">Missatge:</label><br>
                    <textarea id="message" name="message" rows="4" required></textarea><br><br>
                    
                    <input type="submit" value="Enviar">
                </form>
            </article>
        </section>
    </main>
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>