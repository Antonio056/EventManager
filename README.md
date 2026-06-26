# GestionEventi - sito web dinamico

Questo progetto è un sito web dinamico realizzato con **Flask + SQLite** per il database **GestionEventi**.

## Funzionalità incluse

- Registrazione cliente
- Login
- Logout
- Modifica password
- Lista eventi
- Dettaglio evento
- Aggiunta/rimozione eventi dai preferiti
- Acquisto biglietti
- Area profilo con biglietti acquistati e preferiti
- Pagine artisti, sponsor e database
- Database SQLite creato automaticamente al primo avvio

## Come avviarlo

1. Apri la cartella del progetto.
2. Installa Flask:

```bash
pip install -r requirements.txt
```

3. Avvia il sito:

```bash
python app.py
```

4. Apri il browser su:

```text
http://127.0.0.1:5000
```

## Account demo

Puoi registrarti da zero oppure usare:

```text
email: demo@gestioneventi.it
password: demo123
```

## Nota

Il modello segue lo schema finale: **PAGAMENTO non è una tabella separata**.  
Gli attributi di pagamento sono integrati nella tabella **BIGLIETTO**.
