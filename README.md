# TDIW - Botiga online de productes musicals

Aquest repositori conté una aplicació web de compra online desenvolupada en PHP amb una estructura MVC i base de dades PostgreSQL. La idea principal és oferir una botiga de productes musicals (guitars, instruments i accessoris) amb catàlegs, carretó de compra, registre d'usuaris, perfil i historial de comandes.

## Descripció del projecte

La pràctica consisteix en construir una botiga en línia amb les funcionalitats bàsiques d'un e-commerce:

- visualització de categories i productes
- navegació per productes amb AJAX
- detall de producte
- afegir i gestionar elements al carretó
- registre i inici de sessió d'usuaris
- actualització del perfil i càrrega d'imatges
- confirmació i guardat de comandes
- historial de compres
- validacions de seguretat i protecció contra SQL injection/XSS

El projecte està organitzat en tres parts principals:

- `codigo/plantilla`: base de la aplicació i referència del projecte
- `codigo/public_html`: versió del projecte per desplegament web pública
- `codigo/query.sql`: scripts SQL de la base de dades

## Estructura del repositori

```text
tdiw/
├── .git/
├── codigo/
│   ├── query.sql
│   ├── RESUM_PRACTICA.md
│   ├── RESUM_RAPIDO.md
│   ├── TODO.txt
│   ├── plantilla/
│   │   ├── database.sql
│   │   ├── index.php
│   │   ├── script.js
│   │   ├── style.css
│   │   ├── controllers/
│   │   │   ├── CartController.php
│   │   │   ├── PerfilController.php
│   │   │   └── ProductoController.php
│   │   ├── models/
│   │   │   ├── actualitzausuari.php
│   │   │   ├── connectaBD.php
│   │   │   ├── consultaCategories.php
│   │   │   ├── consultaProductes.php
│   │   │   ├── guardaComanda.php
│   │   │   └── registrausuari.php
│   │   ├── views/
│   │   │   ├── cart.php
│   │   │   ├── confirmacio_comanda.php
│   │   │   ├── editar-perfil.php
│   │   │   ├── historialComandes.php
│   │   │   ├── home.php
│   │   │   ├── iniciarsesio.php
│   │   │   ├── llistatCategories.php
│   │   │   ├── perfil.php
│   │   │   ├── register.php
│   │   │   └── partials/
│   │   │       ├── cart-sidebar.php
│   │   │       ├── footer.php
│   │   │       └── header.php
│   │   └── uploadedFiles/
│   └── public_html/
│       ├── index.php
│       ├── script.js
│       ├── style.css
│       ├── todo.txt
│       ├── controllers/
│       │   ├── CartController.php
│       │   ├── ContacteController.php
│       │   ├── HomeController.php
│       │   ├── PerfilController.php
│       │   └── ProductoController.php
│       ├── models/
│       │   ├── actualitzausuari.php
│       │   ├── connectaBD.php
│       │   ├── consultaCategories.php
│       │   ├── consultaComandes.php
│       │   ├── consultaDetallComanda.php
│       │   ├── consultaProductes.php
│       │   ├── guardaCabas.php
│       │   ├── guardaComanda.php
│       │   └── registrausuari.php
│       ├── views/
│       │   ├── cart.php
│       │   ├── confirmacio_comanda.php
│       │   ├── contacte.php
│       │   ├── editar-perfil.php
│       │   ├── historialComandes.php
│       │   ├── home.php
│       │   ├── iniciarsesio.php
│       │   ├── llistatCategories.php
│       │   ├── perfil.php
│       │   ├── register.php
│       │   └── partials/
│       │       ├── cart-sidebar.php
│       │       ├── footer.php
│       │       └── header.php
│       ├── images/
│       └── uploadedFiles/
└── README.md
```

## Tecnologies utilitzades

- PHP
- PostgreSQL / SQL
- JavaScript + Fetch/AJAX
- HTML5 + CSS3
- Sessions de PHP
- Arquitectura MVC

## Patró arquitectònic

La aplicació segueix el patró MVC:

- Models: gestionen la connexió i les consultes a la base de dades
- Views: mostren la interfície d'usuari i el contingut HTML
- Controllers: coordinen la lògica, processen peticions i decideixen quina vista retornar
- index.php: actua com a enrutador central

Això permet separar la lògica de negoci de la presentació i facilita mantenir el projecte i ampliar funcionalitats.

## Funcionalitats principals

### Catàleg de productes

- llistat de categories
- visualització de productes per categoria
- cerca de productes per nom/descripció
- navegació dinàmica sense recàrrega de pàgina mitjançant AJAX

### Carretó de compra

- afegir productes al carretó
- modificar quantitats
- eliminar productes
- mostrar total i contingut de la compra
- sincronització amb la navegació en temps real

### Perfil i autenticació

- registre d'usuari
- inici de sessió
- gestió de session en PHP
- actualització del perfil
- càrrega i emmagatzematge d'imatges de perfil

### Comandes

- confirmació de compra
- generació de comandes amb les seves línies
- emmagatzematge a la base de dades
- historial de comandes per usuari

### Seguretat

- consultes SQL parametritzades
- hash de contrasenyes amb `password_hash()`
- validació d'entrada i filtres de dades
- protecció contra XSS amb `htmlspecialchars()`
- validació de tipus MIME i mida d'imatges

## Base de dades

El projecte utilitza una base de dades PostgreSQL i inclou els scripts necessaris per crear-la i inicialitzar-la. La informació principal es guarda en taules com:

- usuaris
- categories
- productes
- comandes
- línies de comanda

Els fitxers rellevants són:

- `codigo/query.sql`
- `codigo/plantilla/database.sql`

## Execució

Per executar el projecte localment cal tenir instal·lat un servidor web compatible amb PHP i PostgreSQL.

### Passos bàsics

1. Crear la base de dades PostgreSQL
2. Executar el script SQL
3. Configurar la connexió a la BD a `connectaBD.php`
4. Posar la carpeta del projecte dins del directori web del servidor
5. Accedir mitjançant el navegador

Exemple de ruta local:

```text
http://localhost/plantilla/index.php
```

## Notes importants

Aquest repositori inclou documentació addicional a:

- `codigo/RESUM_PRACTICA.md`
- `codigo/RESUM_RAPIDO.md`
- `codigo/TODO.txt`

Aquests documents expliquen la pràctica i els requisits que s'han d'assolir, així com observacions útils per a la presentació o l'examen.

## Objectiu del projecte

El repositori és una pràctica de desenvolupament web orientada a la creació d'una botiga online completa, amb els requisits típics d'una assignatura de Tecnologia de Desenvolupament per a Internet i Web.

Es tracta d'un projecte apropiat per entendre:

- arquitectura MVC
- interacció amb bases de dades
- AJAX i navegació dinàmica
- gestió de sessions i autenticació
- lògica de compra i perfil d'usuari

## Resum

En conclusió, aquest repositori mostra una implementació completa d'una botiga online amb una estructura clara i modular, enfocada a l'aprenentatge de desenvolupament web front-end i back-end amb PHP i SQL.
