CREATE TABLE Categoria (
    id SERIAL PRIMARY KEY NOT NULL UNIQUE,
    nom VARCHAR NOT NULL UNIQUE,
    descripcio TEXT
);

CREATE TABLE Comanda (
    id SERIAL PRIMARY KEY NOT NULL UNIQUE,
    estat VARCHAR DEFAULT 'Pendent',
    preuTotal DOUBLE PRECISION NOT NULL,
    data_comanda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_usuari INTEGER NOT NULL,
    CONSTRAINT chk_estat CHECK (estat IN ('Pendent', 'Enviat', 'Rebutjat', 'Cancel·lat', 'Finalitzat'))
);

CREATE TABLE LiniaComanda (
    id SERIAL PRIMARY KEY NOT NULL UNIQUE,
    unitats INTEGER NOT NULL CHECK (unitats > 0),
    id_producte INTEGER NOT NULL,
    id_comanda INTEGER NOT NULL
);

CREATE TABLE Producte (
    id SERIAL PRIMARY KEY NOT NULL UNIQUE,
    nom VARCHAR NOT NULL,
    descripcio TEXT NOT NULL,
    preu DOUBLE PRECISION NOT NULL,
    imatge VARCHAR,
    actiu BOOLEAN DEFAULT TRUE,
    id_categoria INTEGER NOT NULL
);

CREATE TABLE Usuari (
    id SERIAL PRIMARY KEY NOT NULL UNIQUE,
    nom VARCHAR NOT NULL,
    email VARCHAR NOT NULL UNIQUE,
    password_hash VARCHAR NOT NULL,
    adreca VARCHAR,
    poblacio VARCHAR,
    codi_postal VARCHAR,
    foto_perfil VARCHAR,
    CONSTRAINT chk_codi_postal CHECK (codi_postal ~ '^\d{5}$')
);

ALTER TABLE Producte
    ADD CONSTRAINT fk_categoria_to_producte
    FOREIGN KEY (id_categoria)
    REFERENCES Categoria(id);

ALTER TABLE Comanda
    ADD CONSTRAINT fk_usuari_to_comanda
    FOREIGN KEY (id_usuari)
    REFERENCES Usuari(id);

ALTER TABLE LiniaComanda
    ADD CONSTRAINT fk_producte_to_liniacomanda
    FOREIGN KEY (id_producte)
    REFERENCES Producte(id);

ALTER TABLE LiniaComanda
    ADD CONSTRAINT fk_comanda_to_liniacomanda
    FOREIGN KEY (id_comanda)
    REFERENCES Comanda(id);