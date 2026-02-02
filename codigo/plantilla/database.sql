-- ================================================================
-- PLANTILLA BD - TDIW BOTIGA ONLINE
-- ================================================================

-- Crear taules
CREATE TABLE categoria (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    descripcio TEXT,
    images TEXT
);

CREATE TABLE producte (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    descripcio TEXT,
    preu DECIMAL(10,2) NOT NULL,
    images TEXT,
    categoria_id INTEGER REFERENCES categoria(id),
    actiu BOOLEAN DEFAULT true
);

CREATE TABLE usuari (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    adreca VARCHAR(255),
    poblacio VARCHAR(100),
    codi_postal VARCHAR(5),
    foto_perfil TEXT
);

CREATE TABLE comanda (
    id SERIAL PRIMARY KEY,
    usuari_id INTEGER REFERENCES usuari(id),
    data_comanda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    estat VARCHAR(50) DEFAULT 'pendent'
);

CREATE TABLE liniacomanda (
    id SERIAL PRIMARY KEY,
    comanda_id INTEGER REFERENCES comanda(id),
    producte_id INTEGER REFERENCES producte(id),
    quantitat INTEGER NOT NULL,
    preu_unitari DECIMAL(10,2) NOT NULL
);

-- ================================================================
-- DADES DE PROVA
-- ================================================================

-- Inserir categories
INSERT INTO categoria (nom, descripcio, images) VALUES
('Acústics', 'Guitares acústiques de qualitat', 'images/acustics/category.jpg'),
('Elèctriques', 'Guitares elèctriques modernes', 'images/electrics/category.jpg'),
('Clàssiques', 'Guitares clàssiques tradicionals', 'images/classics/category.jpg'),
('Bass', 'Baixos i contrabaixos', 'images/bass/category.jpg');

-- Inserir productes (Categoria 1 - Acústics)
INSERT INTO producte (nom, descripcio, preu, images, categoria_id, actiu) VALUES
('Acoustic Pro 100', 'Guitarra acústica professional amb fusta de qualitat', 299.99, 'images/acustics/acoustic1.jpg', 1, true),
('Acoustic Starter', 'Guitarra acústica perfecta per principiants', 149.99, 'images/acustics/acoustic2.jpg', 1, true),
('Acoustic Deluxe', 'Guitarra acústica amb sonoritat excepcional', 449.99, 'images/acustics/acoustic3.jpg', 1, true);

-- Inserir productes (Categoria 2 - Elèctriques)
INSERT INTO producte (nom, descripcio, preu, images, categoria_id, actiu) VALUES
('Electric Storm', 'Guitarra elèctrica amb pickup personalitzat', 399.99, 'images/electrics/electric1.jpg', 2, true),
('Electric Vibe', 'Guitarra elèctrica amb so vintage', 349.99, 'images/electrics/electric2.jpg', 2, true),
('Electric Future', 'Guitarra elèctrica moderna amb efectes', 549.99, 'images/electrics/electric3.jpg', 2, true);

-- Inserir productes (Categoria 3 - Clàssiques)
INSERT INTO producte (nom, descripcio, preu, images, categoria_id, actiu) VALUES
('Classical Master', 'Guitarra clàssica per a mestres', 499.99, 'images/classics/classical1.jpg', 3, true),
('Classical Student', 'Guitarra clàssica per a estudiants', 199.99, 'images/classics/classical2.jpg', 3, true),
('Classical Heritage', 'Guitarra clàssica amb heritage tradicional', 699.99, 'images/classics/classical3.jpg', 3, true);

-- Inserir productes (Categoria 4 - Bass)
INSERT INTO producte (nom, descripcio, preu, images, categoria_id, actiu) VALUES
('Bass Thunder', 'Baix elèctric amb so potent', 299.99, 'images/bass/bass1.jpg', 4, true),
('Bass Wave', 'Baix acústic amb sonoritat càlida', 349.99, 'images/bass/bass2.jpg', 4, true),
('Bass Pro', 'Baix professional per a gira mundial', 599.99, 'images/bass/bass3.jpg', 4, true);

-- ================================================================
-- VERIFICACIONS
-- ================================================================

SELECT * FROM categoria;
SELECT * FROM producte WHERE actiu = true;
SELECT COUNT(*) as total_productes FROM producte WHERE actiu = true;
