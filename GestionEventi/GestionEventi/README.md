# GestionEventi

Prototipo di sito web PHP/MySQL per la gestione di eventi musicali, spettacoli e concerti.

## Funzioni incluse

- Dashboard con conteggi e riepilogo vendite.
- Gestione luoghi: nome, indirizzo, città, capienza.
- Gestione clienti, artisti e sponsor come sotto-entità di `SOGGETTO`.
- Registrazione eventi con data, ora, luogo, descrizione, genere, artisti e sponsor.
- Biglietteria: vendita biglietti con prezzo, tipo, posto, stato e pagamento associato.
- Storico pagamenti.

## Requisiti

- XAMPP / MAMP / Laragon oppure ambiente PHP 8+ con MariaDB/MySQL.
- Estensione PHP PDO MySQL abilitata.

## Installazione con XAMPP

1. Copia la cartella `GestionEventi` dentro `htdocs`.
2. Avvia Apache e MySQL da XAMPP.
3. Apri phpMyAdmin e crea/importa il database usando il file:
   `database/dump1.sql`
4. Controlla le credenziali in `includes/db.php`:
   - host: `127.0.0.1`
   - database: `gestione_eventi`
   - utente: `root`
   - password: vuota, salvo diversa configurazione locale.
5. Apri nel browser:
   `http://localhost/GestionEventi/`

## Ordine consigliato di inserimento dati

1. Luoghi
2. Clienti
3. Artisti
4. Sponsor
5. Eventi
6. Biglietti

Questo ordine rispetta le chiavi esterne presenti nello schema logico.

## Tabelle usate

- `soggetto`
- `cliente`
- `artista`
- `sponsor`
- `luogo`
- `evento`
- `pagamento`
- `biglietto`
- `evento_artista`
- `evento_sponsor`
