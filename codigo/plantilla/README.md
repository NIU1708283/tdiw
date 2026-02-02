# PLANTILLA COMPLETA - BOTIGA ONLINE TDIW

Una plantilla completa i simple de botiga online amb tots els requisits de l'examen.

## 📋 ESTRUCTURA DE FITXERS

```
plantilla/
├── index.php                      ← Router principal
├── script.js                      ← AJAX/Fetch + DOM
├── style.css                      ← Estils CSS3
├── database.sql                   ← Script SQL per crear BD
├── controllers/
│   ├── ProductoController.php     ← Gestió de categories i productes
│   ├── CartController.php         ← Gestió del carretó
│   └── PerfilController.php       ← Gestió de usuaris
├── models/
│   ├── connectaBD.php             ← Connexió PostgreSQL
│   ├── consultaCategories.php     ← Consultes categories
│   ├── consultaProductes.php      ← Consultes productes
│   ├── registrausuari.php         ← Registre i login
│   ├── actualitzausuari.php       ← Actualitzar perfil
│   └── guardaComanda.php          ← Guardar comandes
├── views/
│   ├── home.php                   ← Pàgina principal
│   ├── llistatCategories.php      ← Categories i productes
│   ├── cart.php                   ← Carretó de compra
│   ├── register.php               ← Formulari registre
│   ├── iniciarsesio.php           ← Formulari login
│   ├── editar-perfil.php          ← Editar perfil i foto
│   ├── perfil.php                 ← Visió de perfil
│   ├── confirmacio_comanda.php    ← Confirmació de compra
│   ├── historialComandes.php      ← Historial de comandes
│   └── partials/
│       ├── header.php             ← Navbar i estructura HTML
│       ├── footer.php             ← Footer i tancament
│       └── cart-sidebar.php       ← Carretó lateral
├── uploadedFiles/                 ← Fotos de perfil dels usuaris
└── images/                        ← Imatges de categories/productes
```

---

## 🚀 INSTRUCCIONS D'INSTAL·LACIÓ

### 1. Crear la Base de Dades

Copiar el contingut de `database.sql` i executar-lo a PostgreSQL:

```bash
psql -U postgres -d postgres -f database.sql
```

O manualmente a pgAdmin/PHPMyAdmin:
- Crear BD: `tdiw_botiga`
- Executar les taules del fitxer `database.sql`

### 2. Configurar Connexió a BD

Editar `models/connectaBD.php`:

```php
$host = 'localhost';
$dbname = 'tdiw_botiga';    // Nom de la vostra BD
$user = 'postgres';          // Usuari PostgreSQL
$password = 'password';       // Contrasenya
$port = 5432;
```

### 3. Configurar Servidor Web

Copiar la carpeta `plantilla/` a la carpeta pública del vostre servidor (ex: `/var/www/html/` o `C:\xampp\htdocs\`)

### 4. Accedir a la Web

```
http://localhost/plantilla/index.php
```

---

## ✅ REQUISITS COMPLETATS

### ✅ SESSIÓ 1 - Layout HTML5 + CSS3
- [x] Estructura HTML5 semàntica
- [x] CSS3 responsive
- [x] Formularis HTML5 amb validació

### ✅ SESSIÓ 2 - Arquitectura MVC
- [x] Router central (`index.php`)
- [x] Controllers (`ProductoController`, `CartController`, `PerfilController`)
- [x] Models (funcions de BD)
- [x] Views (vistes PHP)
- [x] BD PostgreSQL amb taules relacionades

### ✅ SESSIÓ 3 - AJAX/Fetch
- [x] **Caregar categories sense recarregar** - `loadCategory()`
- [x] **Detall de producte AJAX** - `loadProductDetail()`
- [x] **Registre d'usuaris** amb `password_hash()`
- [x] **Consultes parametritzades** contra SQL Injection

### ✅ SESSIÓ 4 - Validació i Seguretat
- [x] **Validació servidor** amb `filter_var()`
- [x] **Protecció XSS** amb `htmlspecialchars()`
- [x] **Login amb sessions**
- [x] **AJAX carretó** sense recarregar
- [x] **Carretó visible** amb sidebar
- [x] **Pàgina del carretó** amb modificació de quantitats

### ✅ SESSIÓ 5 - Comandes i Perfil
- [x] **Confirmar comanda** i guardar a BD
- [x] **Editar perfil**
- [x] **PUJADA D'IMATGES** 📷
  - Validació MIME type (JPG, PNG, GIF)
  - Validació mida (màxim 5MB)
  - Ruta correcta: `uploadedFiles/{timestamp}_{uniqid}.{ext}`
  - Guarda només el nom a BD, no la ruta completa
  - Recupera a la vista: `uploadedFiles/{foto_perfil}`
- [x] **Historial de comandes**
- [x] **Transaccions BD** (comanda + línies simultàniament)

### ✅ SESSIÓ 6 (Optional)
- [x] Buscador global AJAX
- [x] Búsqueda dins de categoria
- [x] Búsqueda parametritzada contra SQL Injection

---

## 🎯 PUNTS CRÍTICS PER A L'EXAMEN

### 1. AJAX/Fetch - Caregar Categories
```javascript
// script.js - loadCategory()
fetch(`index.php?action=categoria&cat=${categoryId}`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(res => res.json())
.then(data => { /* renderizar */ })
```

### 2. Crear Categories des de BD
```php
// models/consultaCategories.php
function consultaCategories($connexio) {
    $query = "SELECT id, nom, descripcio, images FROM categoria";
    $result = pg_query($connexio, $query);
    return pg_fetch_all($result) ?: [];
}
```

### 3. PUJADA D'IMATGES - Ruta Correcta ✅
```php
// controllers/PerfilController.php - actualitzarPerfil()
$nom_fitxer = time() . '_' . uniqid() . '.jpg';
$ruta_destino = __DIR__ . '/../uploadedFiles/' . $nom_fitxer;
move_uploaded_file($file['tmp_name'], $ruta_destino);
// Guardar a BD: NOMÉS $nom_fitxer, no la ruta completa

// Vista: uploadedFiles/{$foto_perfil}
<img src="uploadedFiles/<?= $usuari['foto_perfil'] ?>" alt="Foto">
```

### 4. Afegir Productes al Carretó (AJAX)
```javascript
// script.js - addToCartAJAX()
fetch('index.php?action=cart_afegir', {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: formData
})
.then(res => res.json())
.then(data => {
    updateCartUI(data.cart, data.count);
})
```

### 5. Actualizar Comptadors i Carretó
```javascript
// script.js - updateCartUI()
document.getElementById('cart-count').textContent = count;
document.getElementById('cart-total').textContent = total + '€';
// Renderizar items dinàmicament
```

### 6. Consultes Parametritzades (Seguretat)
```php
// models/registrausuari.php
$query = "INSERT INTO usuari (nom, email) VALUES ($1, $2)";
$result = pg_query_params($connexio, $query, array($nom, $email));
// NUNCA concatenar directament: VALUES ('$nom', '$email')
```

---

## 🔒 SEGURETAT

✅ **Implemented:**
- Consultes parametritzades amb `pg_query_params()`
- Contrasenyades xifrades amb `password_hash(PASSWORD_DEFAULT)`
- Validació de tipus MIME per imatges
- Validació de mida de fitxer (5MB)
- Protecció XSS amb `htmlspecialchars()`
- Validació de formularis amb `filter_var()`
- Sessions PHP per autenticació
- Transaccions BD per integritat

---

## 📝 NOTES IMPORTANTS

### Pujada d'Imatges - No oblidar:

1. **Ruta BD**: Guardar **NOMÉS** el nom del fitxer:
   ```php
   'foto_perfil' => '1738516123_abc123.jpg'  ✅ CORRECTE
   'foto_perfil' => '/uploadedFiles/1738516123_abc123.jpg'  ❌ INCORRECT
   ```

2. **Ruta Web**: Al obtenir de BD, afegir prefix:
   ```php
   <img src="uploadedFiles/<?= $usuari['foto_perfil'] ?>" alt="Foto">
   ```

3. **Validacions imprescindibles**:
   - Verificar MIME type: `image/jpeg`, `image/png`, `image/gif`
   - Limitar mida: 5MB màxim
   - Generar nom segur: `time() . '_' . uniqid() . '.ext'`
   - Usar `move_uploaded_file()` (no `copy()`)

### Carretó:
- Guardat a `$_SESSION['cart']` (array)
- Actualitzat amb AJAX sense recarregar
- Comptador visible sempre
- Pàgina del carretó amb taula modificable

### Comanda:
- Guardar simultàniament comanda + línies
- Usar transaccions: `BEGIN`, `COMMIT`, `ROLLBACK`
- Redirigir a confirmació amb ID

---

## 🛠️ TROUBLESHOOTING

### "No s'ha pogut connectar a la BD"
- Verificar credencials a `connectaBD.php`
- PostgreSQL está funcionant?
- BD `tdiw_botiga` creada?

### "Foto no es guarda"
- Verificar carpeta `uploadedFiles/` té permisos d'escriptura (755)
- Verificar ruta correcta: `__DIR__ . '/../uploadedFiles/'`
- MIME type és correcte?

### "AJAX no funciona"
- Verificar header: `'X-Requested-With': 'XMLHttpRequest'`
- Verificar controller retorna JSON
- Mirar consola del navegador (F12)

---

## ✨ PERSONALITZACIÓ

Per adaptar la plantilla a un altre producte (no guitares):

1. **Cambiar categories** a `database.sql`
2. **Cambiar imatges** a carpeta `images/`
3. **Cambiar noms** a vistes HTML
4. **Mantenir estructura MVC igual**

---

**¡Bona suerte en l'examen!** 🎓
