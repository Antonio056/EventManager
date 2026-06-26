DROP TABLE IF EXISTS preferito;
DROP TABLE IF EXISTS biglietto;
DROP TABLE IF EXISTS sponsorizza;
DROP TABLE IF EXISTS contratto;
DROP TABLE IF EXISTS evento;
DROP TABLE IF EXISTS luogo;
DROP TABLE IF EXISTS sponsor;
DROP TABLE IF EXISTS artista;
DROP TABLE IF EXISTS cliente;
DROP TABLE IF EXISTS soggetto;

CREATE TABLE soggetto (
    id_soggetto INTEGER PRIMARY KEY AUTOINCREMENT
);

CREATE TABLE cliente (
    id_cliente INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    cognome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    telefono TEXT,
    password_hash TEXT NOT NULL,
    id_soggetto INTEGER NOT NULL,
    FOREIGN KEY (id_soggetto) REFERENCES soggetto(id_soggetto)
);

CREATE TABLE artista (
    id_artista INTEGER PRIMARY KEY AUTOINCREMENT,
    nome_arte TEXT NOT NULL,
    genere_musicale TEXT,
    nazionalita TEXT,
    email TEXT,
    id_soggetto INTEGER NOT NULL,
    FOREIGN KEY (id_soggetto) REFERENCES soggetto(id_soggetto)
);

CREATE TABLE sponsor (
    id_sponsor INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT,
    settore_commerciale TEXT,
    id_soggetto INTEGER NOT NULL,
    FOREIGN KEY (id_soggetto) REFERENCES soggetto(id_soggetto)
);

CREATE TABLE luogo (
    id_luogo INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    indirizzo TEXT NOT NULL,
    citta TEXT NOT NULL,
    capienza INTEGER NOT NULL
);

CREATE TABLE evento (
    id_evento INTEGER PRIMARY KEY AUTOINCREMENT,
    titolo TEXT NOT NULL,
    data TEXT NOT NULL,
    ora TEXT NOT NULL,
    descrizione TEXT,
    genere TEXT,
    id_luogo INTEGER NOT NULL,
    FOREIGN KEY (id_luogo) REFERENCES luogo(id_luogo)
);

CREATE TABLE biglietto (
    id_biglietto INTEGER PRIMARY KEY AUTOINCREMENT,
    prezzo REAL NOT NULL,
    tipo TEXT NOT NULL,
    importo REAL NOT NULL,
    metodo TEXT NOT NULL,
    esito TEXT NOT NULL,
    posto TEXT NOT NULL,
    stato TEXT NOT NULL,
    data_pagamento TEXT NOT NULL,
    id_cliente INTEGER NOT NULL,
    id_evento INTEGER NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_evento) REFERENCES evento(id_evento)
);

CREATE TABLE contratto (
    id_contratto INTEGER PRIMARY KEY AUTOINCREMENT,
    data_firma TEXT NOT NULL,
    compenso REAL NOT NULL,
    stato_contratto TEXT NOT NULL,
    ruolo_artista TEXT NOT NULL,
    id_artista INTEGER NOT NULL,
    id_evento INTEGER NOT NULL,
    FOREIGN KEY (id_artista) REFERENCES artista(id_artista),
    FOREIGN KEY (id_evento) REFERENCES evento(id_evento)
);

CREATE TABLE sponsorizza (
    id_sponsor INTEGER NOT NULL,
    id_evento INTEGER NOT NULL,
    PRIMARY KEY (id_sponsor, id_evento),
    FOREIGN KEY (id_sponsor) REFERENCES sponsor(id_sponsor),
    FOREIGN KEY (id_evento) REFERENCES evento(id_evento)
);

CREATE TABLE preferito (
    id_cliente INTEGER NOT NULL,
    id_evento INTEGER NOT NULL,
    data_aggiunta TEXT NOT NULL,
    PRIMARY KEY (id_cliente, id_evento),
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_evento) REFERENCES evento(id_evento)
);
