# Índex Ràpid - Localització de Funcionalitats

## 🔎 Busca per Funcionalitat

### 1️⃣ AJAX/Fetch - Navegació sense recarregar

| Funcionalitat | Fitxer | Línies | Codi |
|---|---|---|---|
| Cargar categories | public_html/script.js | 208-230 | `window.loadCategory(catSlug)` |
| Renderitzar productes | public_html/script.js | 232-273 | `renderCategoryProducts()` |
| Detall producte | public_html/script.js | 347-365 | `window.loadProductDetail(id)` |
| Buscador en categoria | public_html/script.js | 276-304 | `searchProducts(category, query)` |
| Buscador global | public_html/script.js | 426-470 | `performGlobalSearch(query)` |
| **Backend categories** | public_html/controllers/ProductoController.php | 52-86 | `categoria()` retorna JSON |
| **Backend producte** | public_html/controllers/ProductoController.php | 87-145 | `detallProducte()` retorna JSON |
| **Backend cerca** | public_html/controllers/ProductoController.php | 146-181 | `buscar()` retorna JSON |

---

### 2️⃣ Creació de Categories

| Funcionalitat | Fitxer | Línies | Descripció |
|---|---|---|---|
| Consultar categories | public_html/models/consultaCategories.php | 1-15 | `consultaCategories($connexio)` |
| Mostrar categories | public_html/views/llistatCategories.php | 35-65 | Grid de categories clicables |
| Protecció XSS | public_html/views/llistatCategories.php | 44-46 | `htmlspecialchars()` |
| Acció AJAX | public_html/script.js | 208-230 | `loadCategory()` amb Fetch |
| Controller | public_html/controllers/ProductoController.php | 52-86 | Detecta AJAX, retorna JSON |

**Base de Dades:**
```sql
CREATE TABLE categoria (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    descripcio TEXT,
    images TEXT
);
```

---

### 3️⃣ Pujada d'Imatges 📷

#### 🎯 Foto de Perfil d'Usuari

| Pas | Fitxer | Línies | Codi |
|---|---|---|---|
| **Vista (formulari)** | public_html/views/editar-perfil.php | 94-101 | `<input type="file" name="foto_perfil">` |
| **Vista (preview)** | public_html/views/editar-perfil.php | 126-134 | `<img src="uploadedFiles/...">` |
| **Controller (validació)** | public_html/controllers/PerfilController.php | 89-123 | Validació MIME, mida, seguretat |
| **Directori destí** | public_html/uploadedFiles/ | - | Carpeta per fitxers pujats |
| **Ruta BD** | public_html/models/actualitzausuari.php | - | Guarda nom a `usuari.foto_perfil` |

**Fluxe complet:**
1. Usuari selecciona fitxer en `editar-perfil.php`
2. Enviat POST a `index.php?action=actualitzar-perfil`
3. `PerfilController::actualitzarPerfil()` valida:
   - Tipus MIME: `image/jpeg`, `image/png`, `image/gif`
   - Mida màxima: 5MB
4. Genera nom segur: `{timestamp}_{uniqid}.{extensió}`
5. Mou a `/uploadedFiles/{nom}`
6. Guarda a BD el camp `foto_perfil`
7. Recupera en vista: `uploadedFiles/{foto_perfil}`

✅ **Ruta Web Correcta**: `uploadedFiles/1738516123_abc123def456.jpg`

---

### 4️⃣ Afegir Productes al Carretó 🛒

#### A. Afegir via AJAX (sense recarregar)

| Pas | Fitxer | Línies | Funció |
|---|---|---|---|
| **Botó HTML** | public_html/script.js | 269 | `onclick="addToCartAJAX(id, nom, preu, imatge)"` |
| **Funció AJAX** | public_html/script.js | 382-403 | `window.addToCartAJAX()` - FormData + Fetch |
| **Backend** | public_html/controllers/CartController.php | 19-59 | `afegir()` - Afegeix a `$_SESSION['cart']` |
| **Actualitzar UI** | public_html/script.js | 120-165 | `window.updateCartUI(cart, count)` |
| **Notificació** | public_html/script.js | 30-42 | `window.showCartNotification()` |

#### B. Estructura del Carretó

```php
$_SESSION['cart'] = [
    [
        'id' => 1,
        'nom' => 'Producte A',
        'preu' => 15.99,
        'imatge' => 'images/producte1.jpg',
        'quantitat' => 2  // 🔑 SUMA QUANTITATS SI JA EXISTEIX
    ],
    ...
]
```

#### C. Comptadors Actuals

| Element | Ubicació | Actualització |
|---|---|---|
| Comptador sidebar | `#cart-count` | Actualitzat en cada `addToCartAJAX()` |
| Total preu sidebar | `#cart-total` | Calculat en `window.updateCartUI()` |
| Llistat items | `#cart-items` | Generats dinàmicament |

#### D. Pàgina del Carretó

| Funcionalitat | Fitxer | Línies |
|---|---|---|
| Taula dinàmica | public_html/views/cart.php | 33-197 |
| Carregar items | public_html/script.js (cart.php) | 47-97 |
| Modificar quantitat | public_html/script.js (cart.php) | 110-125 |
| Eliminar item | public_html/script.js (cart.php) | 129-143 |
| Buidar carretó | public_html/script.js (cart.php) | 9-21 |
| Finalitzar compra | public_html/script.js (cart.php) | 23-25 |

---

### 5️⃣ Afegir Nous Productes ➕

| Operació | Fitxer | Controller | Model |
|---|---|---|---|
| **Crear producte (Backend - No visible a web)** | - | - | `INSERT INTO producte(...)` |
| **Llistar productes** | public_html/views/llistatCategories.php | ProductoController::botiga() | consultaProductes.php |
| **Cerca productes** | public_html/script.js | ProductoController::categoria() | cercaProductesEnCategoria() |
| **Afegir al carretó** | public_html/views/cart.php | CartController::afegir() | - |

**Base de Dades:**
```sql
CREATE TABLE producte (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    descripcio TEXT,
    preu DECIMAL(10, 2) NOT NULL,
    imatge TEXT,
    id_categoria INTEGER REFERENCES categoria(id),
    actiu BOOLEAN DEFAULT true
);
```

---

### 6️⃣ Registre i Login 👤

#### A. Registre d'Usuaris

| Pas | Fitxer | Línies | Funció |
|---|---|---|---|
| **Vista formulari** | public_html/views/register.php | - | HTML5 amb validació |
| **Validació servidor** | public_html/models/registrausuari.php | 7-40 | `registra_usuari()` amb `filter_var()` |
| **Hash contrasenya** | public_html/models/registrausuari.php | 47 | `password_hash($password, PASSWORD_DEFAULT)` |
| **Consulta parametritzada** | public_html/models/registrausuari.php | 52 | `pg_query_params($conn, $query, $params)` |
| **Cotxe** | public_html/controllers/PerfilController.php | 269-327 | `registrarse()` |

#### B. Login/Inici de Sessió

| Pas | Fitxer | Línies | Funció |
|---|---|---|---|
| **Vista formulari** | public_html/views/iniciarsesio.php | - | HTML5 + modal AJAX |
| **Controller** | public_html/controllers/PerfilController.php | 225-243 | `iniciarSessio()` |
| **Model verificació** | public_html/models/registrausuari.php | 72-108 | `verifica_usuari()` amb `password_verify()` |
| **Sessió** | public_html/controllers/PerfilController.php | 231-239 | `$_SESSION['usuari'] = [...]` |

#### C. Logout

| Tipus | Fitxer | Línies | Comportament |
|---|---|---|---|
| **AJAX logout** | public_html/views/partials/header.php | 76-84 | Fetch + reload |
| **Controller** | public_html/controllers/PerfilController.php | 195-210 | Destrueix `$_SESSION['usuari']` |

---

### 7️⃣ Menú Desplegable d'Usuari 👥

| Estat | Menú Mostra | Fitxer | Línies |
|---|---|---|---|
| **No loguejat** | Iniciar Sessió / Registrar-se | public_html/views/partials/header.php | 48-56 |
| **Loguejat** | El meu Perfil / Les meves comandes / Tancar Sessió | public_html/views/partials/header.php | 41-45 |
| **jQuery toggle** | script.js | 52-63 | Dropdown .show class |

---

### 8️⃣ Confirmació de Comanda ✅

| Pas | Fitxer | Línies | Detalls |
|---|---|---|---|
| **Verificar usuari loguejat** | public_html/controllers/CartController.php | 155-160 | Redirigeix a login si no |
| **Verificar carretó no buit** | public_html/controllers/CartController.php | 162-165 | Redirigeix si buit |
| **Calcular total** | public_html/controllers/CartController.php | 168 | `array_sum(array_map(...))` |
| **Guardar comanda (BD)** | public_html/models/guardaComanda.php | 1-60 | `guardar_comanda()` amb transacció |
| **Guardar línies comanda** | public_html/models/guardaComanda.php | 30-55 | `INSERT INTO liniacomanda` |
| **Buidar carretó** | public_html/controllers/CartController.php | 181 | `$_SESSION['cart'] = []` |
| **Redirigir confirmació** | public_html/controllers/CartController.php | 183 | `confirmacio_comanda?id=$idComanda` |
| **Vista confirmació** | public_html/views/confirmacio_comanda.php | - | Mostra resum |

**Transacció BD:**
```sql
BEGIN;
INSERT INTO comanda(...) VALUES(...);
INSERT INTO liniacomanda(...) VALUES(...);
COMMIT;
```

---

### 9️⃣ Edició de Perfil i Canvi de Contrasenya 🔐

| Funcionalitat | Fitxer | Línies | Detalles |
|---|---|---|---|
| **Vista formulari** | public_html/views/editar-perfil.php | 96-172 | Camps precarregats de BD |
| **Cargar dades** | public_html/views/editar-perfil.php | 15-23 | Consulta `SELECT * FROM usuari` |
| **Validacions** | public_html/controllers/PerfilController.php | 56-85 | `filter_var()`, regex codi postal |
| **Verificar contrasenya antiga** | public_html/controllers/PerfilController.php | 157-164 | `password_verify()` |
| **Hash nova contrasenya** | public_html/controllers/PerfilController.php | 166 | `password_hash()` |
| **Model actualització** | public_html/models/actualitzausuari.php | - | `UPDATE usuari` parametritzat |

---

### 🔟 Historial de Comandes 📜

| Pas | Fitxer | Línies | Funció |
|---|---|---|---|
| **Controller** | public_html/controllers/PerfilController.php | 35-42 | `historialComandes()` |
| **Model** | public_html/models/consultaComandes.php | - | `obtenir_comandes_usuari($idUsuari)` |
| **Vista** | public_html/views/historialComandes.php | - | Taula de comandes |
| **Detall comanda** | public_html/controllers/CartController.php | 211-224 | `detallComanda()` |

---

## 📊 Taula de Sessions Completades

| Sessió | Requisit | Completat | Fitxers Claus |
|---|---|---|---|
| **1** | Layout HTML5 + CSS3 | ✅ | public_html/views/*.php, style.css |
| **2** | Estructura MVC | ✅ | public_html/index.php, controllers/, models/, views/ |
| **2** | Categories a BD | ✅ | public_html/models/consultaCategories.php |
| **3** | AJAX categories | ✅ | public_html/script.js::loadCategory() |
| **3** | AJAX detall producte | ✅ | public_html/script.js::loadProductDetail() |
| **3** | Registre amb hash | ✅ | public_html/models/registrausuari.php |
| **3** | Consultes parametritzades | ✅ | pg_query_params() |
| **4** | Validació servidor | ✅ | filter_var() |
| **4** | Filtratge XSS | ✅ | htmlspecialchars() |
| **4** | Login amb session | ✅ | public_html/controllers/PerfilController.php |
| **4** | AJAX carretó | ✅ | public_html/script.js::addToCartAJAX() |
| **4** | Carretó visible | ✅ | public_html/views/partials/cart-sidebar.php |
| **4** | Pàgina carretó | ✅ | public_html/views/cart.php |
| **5** | Confirmar comanda | ✅ | public_html/controllers/CartController.php::finalitzarCompra() |
| **5** | Guardar a BD | ✅ | public_html/models/guardaComanda.php |
| **5** | Editar perfil | ✅ | public_html/controllers/PerfilController.php::actualitzarPerfil() |
| **5** | **Pujada foto** | ✅ | PerfilController.php (línies 89-123) |
| **5** | **Ruta correcta** | ✅ | uploadedFiles/{nom_fitxer} |
| **5** | Historial comandes | ✅ | public_html/models/consultaComandes.php |
| **6** | Protecció fitxers | ✅ | MIME type, mida, extensió |
| **Optional** | Buscador global | ✅ | public_html/script.js::performGlobalSearch() |
| **Optional** | Mode fosc | ✅ | public_html/script.js (línies 60-88) |

---

## 🎯 Punts Crítics de Revisió

### ✅ AJAX/Fetch
- [x] `script.js::loadCategory()` - Fetch amb headers XMLHttpRequest
- [x] `script.js::loadProductDetail()` - Retorna JSON
- [x] `script.js::addToCartAJAX()` - FormData + POST + actualitza UI
- [x] `script.js::updateCartUI()` - Renderitza dinàmicament
- [x] Respostes JSON en tots els controllers

### ✅ Creació de Categories
- [x] Lectura de BD en `models/consultaCategories.php`
- [x] Renderització en `views/llistatCategories.php`
- [x] Protecció XSS amb `htmlspecialchars()`
- [x] AJAX load sense recarregar

### ✅ Pujada d'Imatges
- [x] **Ruta**: `uploadedFiles/{timestamp}_{uniqid}.{extensió}`
- [x] Validació MIME type (image/jpeg, image/png, image/gif)
- [x] Validació mida (màxim 5MB)
- [x] `move_uploaded_file()` segur
- [x] BD guarda nom fitxer, no ruta completa

### ✅ Carretó de Compra
- [x] Afegir productes via AJAX
- [x] Actualizar comptador sense recarregar
- [x] Modificar quantitats
- [x] Eliminar items
- [x] Buidar carretó
- [x] Sidebar flotant visible
- [x] Pàgina de carretó completa
- [x] Total calculat correctament

### ✅ Alta de Nous Productes
- [x] Backend suporta inserció a BD
- [x] Llistar funciona correctament
- [x] Cerca funciona per nom i descripció
- [x] Camp `actiu` filtra productes inactius

---

## 📂 Estructura de Fitxers

```
public_html/
├── index.php                          # 📍 ROUTER PRINCIPAL (linies 1-172)
├── script.js                          # 📍 AJAX/Fetch/UI (línies claus: 208, 276, 347, 382)
├── style.css                          # Estils
├── controllers/
│   ├── CartController.php             # ✅ cart_afegir, cart_modificar, cart_eliminar, cart_buidar
│   ├── ProductoController.php         # ✅ categoria (AJAX JSON), producte, buscar
│   ├── PerfilController.php           # ✅ registrarse, iniciarSessio, actualitzarPerfil, logout
│   ├── HomeController.php
│   └── ContacteController.php
├── models/
│   ├── connectaBD.php                 # Connexió PostgreSQL
│   ├── consultaCategories.php         # ✅ consultaCategories()
│   ├── consultaProductes.php          # ✅ consultaProductesPerCategoria, cercaProductesEnCategoria
│   ├── registrausuari.php             # ✅ password_hash, filter_var, password_verify
│   ├── guardaCabas.php                # Sessions carretó
│   ├── guardaComanda.php              # ✅ guardar_comanda() amb transacció
│   ├── actualitzausuari.php           # UPDATE usuari
│   ├── consultaComandes.php           # SELECT comandes
│   └── consultaDetallComanda.php      # Detall comanda
├── views/
│   ├── llistatCategories.php          # ✅ htmlspecialchars() (línies 44-46)
│   ├── cart.php                       # ✅ renderCartTable() AJAX
│   ├── editar-perfil.php              # ✅ foto_perfil upload, passwords
│   ├── confirmacio_comanda.php        # Resum comanda
│   ├── historialComandes.php
│   ├── register.php
│   ├── iniciarsesio.php
│   ├── home.php
│   └── partials/
│       ├── header.php                 # ✅ Menu desplegable, logout AJAX
│       ├── cart-sidebar.php           # ✅ Sidebar flotant
│       └── footer.php
├── uploadedFiles/                     # 📂 Directori de fotos pujades
├── images/                            # Imatges productes
└── tdiw/                              # Git info

---

## 🔐 Seguretat Implementada

| Amenaza | Mitigació | Fitxer | Línies |
|---|---|---|---|
| **SQL Injection** | `pg_query_params()` + placeholders | public_html/models/*.php | - |
| **XSS** | `htmlspecialchars()` + escape en JS | public_html/views/llistatCategories.php | 44-46 |
| **Contrasenya débil** | `password_hash(PASSWORD_DEFAULT)` | public_html/models/registrausuari.php | 47 |
| **Contrasenya sin verificar** | `password_verify()` | public_html/models/registrausuari.php | 100 |
| **Fitxers maliciosos** | Validació MIME + extensió | public_html/controllers/PerfilController.php | 95-100 |
| **Fitxer massa gran** | Límit 5MB | public_html/controllers/PerfilController.php | 104 |
| **Usuari no autenticat** | `isset($_SESSION['usuari'])` checks | public_html/controllers/CartController.php | 155 |

