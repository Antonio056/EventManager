from flask import Flask, render_template, request, redirect, url_for, session, flash, g
import sqlite3
from pathlib import Path
from datetime import datetime
from functools import wraps
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)
app.config["SECRET_KEY"] = "gestion-eventi-secret-key"
DATABASE = Path(__file__).with_name("gestioneventi.db")


def get_db():
    if "db" not in g:
        g.db = sqlite3.connect(DATABASE)
        g.db.row_factory = sqlite3.Row
        g.db.execute("PRAGMA foreign_keys = ON")
    return g.db


@app.teardown_appcontext
def close_db(error=None):
    db = g.pop("db", None)
    if db is not None:
        db.close()


def init_db():
    db = get_db()
    schema_path = Path(__file__).with_name("schema.sql")
    db.executescript(schema_path.read_text(encoding="utf-8"))

    # Utente demo
    cur = db.execute("INSERT INTO soggetto DEFAULT VALUES")
    id_soggetto = cur.lastrowid
    db.execute(
        """INSERT INTO cliente(nome, cognome, email, telefono, password_hash, id_soggetto)
           VALUES (?, ?, ?, ?, ?, ?)""",
        ("Demo", "Cliente", "demo@gestioneventi.it", "3331234567", generate_password_hash("demo123"), id_soggetto)
    )

    # Luoghi
    luoghi = [
        ("Arena Centrale", "Via Roma 12", "Milano", 12000),
        ("Stadio Comunale", "Viale Europa 5", "Roma", 30000),
        ("Teatro Verde", "Piazza Dante 4", "Napoli", 2500),
        ("Palazzetto Eventi", "Corso Torino 18", "Torino", 8000),
    ]
    db.executemany("INSERT INTO luogo(nome, indirizzo, citta, capienza) VALUES (?, ?, ?, ?)", luoghi)

    # Eventi
    eventi = [
        ("Summer Music Night", "2026-07-12", "21:00", "Una serata pop con ospiti speciali, luci e spettacolo live.", "Pop", 1),
        ("Rock Festival", "2026-07-18", "20:30", "Festival rock con band nazionali e internazionali.", "Rock", 2),
        ("Jazz sotto le stelle", "2026-07-25", "22:00", "Concerto jazz in una location elegante e suggestiva.", "Jazz", 3),
        ("Electronic Wave", "2026-08-02", "23:00", "Evento elettronico con DJ set, visual show e area lounge.", "Elettronica", 4),
        ("Indie Live Session", "2026-08-10", "20:00", "Concerto indie con artisti emergenti.", "Indie", 3),
        ("Classic Opera Night", "2026-09-04", "19:30", "Serata dedicata alla musica classica e lirica.", "Classica", 3),
    ]
    db.executemany(
        "INSERT INTO evento(titolo, data, ora, descrizione, genere, id_luogo) VALUES (?, ?, ?, ?, ?, ?)",
        eventi
    )

    # Artisti e contratti
    artisti = [
        ("Luna Voice", "Pop", "Italiana", "luna.voice@example.com", "Cantante principale", 3500, 1),
        ("The Rocks", "Rock", "Italiana", "therocks@example.com", "Band ospite", 5000, 2),
        ("Blue Sax", "Jazz", "Francese", "bluesax@example.com", "Solista", 2200, 3),
        ("DJ Aurora", "Elettronica", "Spagnola", "aurora@example.com", "DJ principale", 4000, 4),
        ("Indie Mood", "Indie", "Italiana", "indie@example.com", "Gruppo principale", 2800, 5),
    ]
    for nome_arte, genere, naz, email, ruolo, compenso, id_evento in artisti:
        cur = db.execute("INSERT INTO soggetto DEFAULT VALUES")
        id_sog = cur.lastrowid
        cur = db.execute(
            "INSERT INTO artista(nome_arte, genere_musicale, nazionalita, email, id_soggetto) VALUES (?, ?, ?, ?, ?)",
            (nome_arte, genere, naz, email, id_sog)
        )
        id_artista = cur.lastrowid
        db.execute(
            """INSERT INTO contratto(data_firma, compenso, stato_contratto, ruolo_artista, id_artista, id_evento)
               VALUES (?, ?, ?, ?, ?, ?)""",
            ("2026-05-20", compenso, "Firmato", ruolo, id_artista, id_evento)
        )

    # Sponsor
    sponsors = [
        ("SoundPlus", "info@soundplus.it", "Audio e tecnologia", [1, 4]),
        ("Urban Food", "sponsor@urbanfood.it", "Ristorazione", [2, 5]),
        ("LightStage", "hello@lightstage.it", "Illuminazione", [3, 4, 6]),
    ]
    for nome, email, settore, eventi_ids in sponsors:
        cur = db.execute("INSERT INTO soggetto DEFAULT VALUES")
        id_sog = cur.lastrowid
        cur = db.execute(
            "INSERT INTO sponsor(nome, email, settore_commerciale, id_soggetto) VALUES (?, ?, ?, ?)",
            (nome, email, settore, id_sog)
        )
        id_sponsor = cur.lastrowid
        for id_evento in eventi_ids:
            db.execute("INSERT INTO sponsorizza(id_sponsor, id_evento) VALUES (?, ?)", (id_sponsor, id_evento))

    db.commit()


@app.before_request
def ensure_database():
    if not DATABASE.exists():
        init_db()


def current_user():
    if "cliente_id" not in session:
        return None
    return get_db().execute("SELECT * FROM cliente WHERE id_cliente = ?", (session["cliente_id"],)).fetchone()


@app.context_processor
def inject_user():
    return {"user": current_user()}


def login_required(view):
    @wraps(view)
    def wrapped_view(*args, **kwargs):
        if "cliente_id" not in session:
            flash("Devi effettuare il login per accedere a questa funzione.", "warning")
            return redirect(url_for("login"))
        return view(*args, **kwargs)
    return wrapped_view


@app.route("/")
def index():
    db = get_db()
    eventi_count = db.execute("SELECT COUNT(*) AS c FROM evento").fetchone()["c"]
    biglietti_count = db.execute("SELECT COUNT(*) AS c FROM biglietto").fetchone()["c"]
    artisti_count = db.execute("SELECT COUNT(*) AS c FROM artista").fetchone()["c"]
    eventi = db.execute("""
        SELECT e.*, l.nome AS luogo, l.citta
        FROM evento e JOIN luogo l ON e.id_luogo = l.id_luogo
        ORDER BY e.data ASC LIMIT 3
    """).fetchall()
    return render_template("index.html", eventi=eventi, eventi_count=eventi_count, biglietti_count=biglietti_count, artisti_count=artisti_count)


@app.route("/registrazione", methods=["GET", "POST"])
def register():
    if request.method == "POST":
        nome = request.form["nome"].strip()
        cognome = request.form["cognome"].strip()
        email = request.form["email"].strip().lower()
        telefono = request.form.get("telefono", "").strip()
        password = request.form["password"]
        conferma = request.form["conferma_password"]

        if password != conferma:
            flash("Le password non coincidono.", "danger")
            return redirect(url_for("register"))

        db = get_db()
        existing = db.execute("SELECT id_cliente FROM cliente WHERE email = ?", (email,)).fetchone()
        if existing:
            flash("Email già registrata. Effettua il login.", "warning")
            return redirect(url_for("login"))

        cur = db.execute("INSERT INTO soggetto DEFAULT VALUES")
        id_soggetto = cur.lastrowid
        cur = db.execute(
            """INSERT INTO cliente(nome, cognome, email, telefono, password_hash, id_soggetto)
               VALUES (?, ?, ?, ?, ?, ?)""",
            (nome, cognome, email, telefono, generate_password_hash(password), id_soggetto)
        )
        db.commit()
        session["cliente_id"] = cur.lastrowid
        flash("Registrazione completata. Benvenuto in GestionEventi!", "success")
        return redirect(url_for("eventi"))

    return render_template("register.html")


@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        email = request.form["email"].strip().lower()
        password = request.form["password"]

        cliente = get_db().execute("SELECT * FROM cliente WHERE email = ?", (email,)).fetchone()
        if cliente and check_password_hash(cliente["password_hash"], password):
            session.clear()
            session["cliente_id"] = cliente["id_cliente"]
            flash("Login effettuato correttamente.", "success")
            return redirect(url_for("eventi"))

        flash("Email o password non corretti.", "danger")

    return render_template("login.html")


@app.route("/logout")
@login_required
def logout():
    session.clear()
    flash("Logout effettuato correttamente.", "success")
    return redirect(url_for("index"))


@app.route("/profilo")
@login_required
def profilo():
    db = get_db()
    id_cliente = session["cliente_id"]
    cliente = current_user()
    biglietti = db.execute("""
        SELECT b.*, e.titolo, e.data, e.ora, l.nome AS luogo, l.citta
        FROM biglietto b
        JOIN evento e ON b.id_evento = e.id_evento
        JOIN luogo l ON e.id_luogo = l.id_luogo
        WHERE b.id_cliente = ?
        ORDER BY b.id_biglietto DESC
    """, (id_cliente,)).fetchall()

    preferiti = db.execute("""
        SELECT e.*, l.nome AS luogo, l.citta
        FROM preferito p
        JOIN evento e ON p.id_evento = e.id_evento
        JOIN luogo l ON e.id_luogo = l.id_luogo
        WHERE p.id_cliente = ?
        ORDER BY p.data_aggiunta DESC
    """, (id_cliente,)).fetchall()

    return render_template("profilo.html", cliente=cliente, biglietti=biglietti, preferiti=preferiti)


@app.route("/modifica-password", methods=["GET", "POST"])
@login_required
def modifica_password():
    if request.method == "POST":
        vecchia = request.form["vecchia_password"]
        nuova = request.form["nuova_password"]
        conferma = request.form["conferma_password"]
        cliente = current_user()

        if not check_password_hash(cliente["password_hash"], vecchia):
            flash("La password attuale non è corretta.", "danger")
            return redirect(url_for("modifica_password"))

        if nuova != conferma:
            flash("Le nuove password non coincidono.", "danger")
            return redirect(url_for("modifica_password"))

        get_db().execute(
            "UPDATE cliente SET password_hash = ? WHERE id_cliente = ?",
            (generate_password_hash(nuova), cliente["id_cliente"])
        )
        get_db().commit()
        flash("Password modificata correttamente.", "success")
        return redirect(url_for("profilo"))

    return render_template("modifica_password.html")


@app.route("/eventi")
def eventi():
    db = get_db()
    q = request.args.get("q", "").strip()
    genere = request.args.get("genere", "").strip()

    sql = """
        SELECT e.*, l.nome AS luogo, l.citta, l.capienza
        FROM evento e
        JOIN luogo l ON e.id_luogo = l.id_luogo
        WHERE 1=1
    """
    params = []
    if q:
        sql += " AND (e.titolo LIKE ? OR e.descrizione LIKE ? OR l.citta LIKE ?)"
        params.extend([f"%{q}%", f"%{q}%", f"%{q}%"])
    if genere:
        sql += " AND e.genere = ?"
        params.append(genere)
    sql += " ORDER BY e.data ASC"

    eventi = db.execute(sql, params).fetchall()
    generi = db.execute("SELECT DISTINCT genere FROM evento ORDER BY genere").fetchall()

    fav_ids = set()
    if "cliente_id" in session:
        rows = db.execute("SELECT id_evento FROM preferito WHERE id_cliente = ?", (session["cliente_id"],)).fetchall()
        fav_ids = {r["id_evento"] for r in rows}

    return render_template("eventi.html", eventi=eventi, generi=generi, q=q, genere=genere, fav_ids=fav_ids)


@app.route("/evento/<int:id_evento>")
def dettaglio_evento(id_evento):
    db = get_db()
    evento = db.execute("""
        SELECT e.*, l.nome AS luogo, l.indirizzo, l.citta, l.capienza
        FROM evento e
        JOIN luogo l ON e.id_luogo = l.id_luogo
        WHERE e.id_evento = ?
    """, (id_evento,)).fetchone()

    if not evento:
        flash("Evento non trovato.", "danger")
        return redirect(url_for("eventi"))

    artisti = db.execute("""
        SELECT a.*, c.ruolo_artista, c.compenso, c.stato_contratto, c.data_firma
        FROM contratto c
        JOIN artista a ON c.id_artista = a.id_artista
        WHERE c.id_evento = ?
    """, (id_evento,)).fetchall()

    sponsors = db.execute("""
        SELECT s.*
        FROM sponsorizza sp
        JOIN sponsor s ON sp.id_sponsor = s.id_sponsor
        WHERE sp.id_evento = ?
    """, (id_evento,)).fetchall()

    is_fav = False
    if "cliente_id" in session:
        is_fav = db.execute(
            "SELECT 1 FROM preferito WHERE id_cliente = ? AND id_evento = ?",
            (session["cliente_id"], id_evento)
        ).fetchone() is not None

    return render_template("dettaglio_evento.html", evento=evento, artisti=artisti, sponsors=sponsors, is_fav=is_fav)


@app.route("/preferito/<int:id_evento>", methods=["POST"])
@login_required
def toggle_preferito(id_evento):
    db = get_db()
    existing = db.execute(
        "SELECT 1 FROM preferito WHERE id_cliente = ? AND id_evento = ?",
        (session["cliente_id"], id_evento)
    ).fetchone()

    if existing:
        db.execute(
            "DELETE FROM preferito WHERE id_cliente = ? AND id_evento = ?",
            (session["cliente_id"], id_evento)
        )
        flash("Evento rimosso dai preferiti.", "success")
    else:
        db.execute(
            "INSERT INTO preferito(id_cliente, id_evento, data_aggiunta) VALUES (?, ?, ?)",
            (session["cliente_id"], id_evento, datetime.now().strftime("%Y-%m-%d %H:%M"))
        )
        flash("Evento aggiunto ai preferiti.", "success")

    db.commit()
    return redirect(request.referrer or url_for("eventi"))


@app.route("/acquista/<int:id_evento>", methods=["GET", "POST"])
@login_required
def acquista(id_evento):
    db = get_db()
    evento = db.execute("""
        SELECT e.*, l.nome AS luogo, l.citta
        FROM evento e JOIN luogo l ON e.id_luogo = l.id_luogo
        WHERE e.id_evento = ?
    """, (id_evento,)).fetchone()

    if not evento:
        flash("Evento non trovato.", "danger")
        return redirect(url_for("eventi"))

    prezzi = {"Standard": 35.00, "VIP": 70.00, "Ridotto": 20.00}

    if request.method == "POST":
        tipo = request.form["tipo"]
        posto = request.form["posto"].strip().upper()
        metodo = request.form["metodo"]
        prezzo = prezzi.get(tipo, 35.00)

        posto_occupato = db.execute(
            "SELECT 1 FROM biglietto WHERE id_evento = ? AND posto = ? AND stato = 'Confermato'",
            (id_evento, posto)
        ).fetchone()

        if posto_occupato:
            flash("Questo posto è già stato acquistato. Scegli un altro posto.", "danger")
            return redirect(url_for("acquista", id_evento=id_evento))

        db.execute(
            """INSERT INTO biglietto(prezzo, tipo, importo, metodo, esito, posto, stato, data_pagamento, id_cliente, id_evento)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)""",
            (
                prezzo, tipo, prezzo, metodo, "Riuscito", posto, "Confermato",
                datetime.now().strftime("%Y-%m-%d %H:%M"),
                session["cliente_id"], id_evento
            )
        )
        db.commit()
        flash("Biglietto acquistato correttamente.", "success")
        return redirect(url_for("profilo"))

    return render_template("acquista.html", evento=evento, prezzi=prezzi)


@app.route("/artisti")
def artisti():
    rows = get_db().execute("""
        SELECT a.*, COUNT(c.id_contratto) AS numero_contratti
        FROM artista a
        LEFT JOIN contratto c ON a.id_artista = c.id_artista
        GROUP BY a.id_artista
        ORDER BY a.nome_arte
    """).fetchall()
    return render_template("artisti.html", artisti=rows)


@app.route("/sponsor")
def sponsor():
    rows = get_db().execute("""
        SELECT s.*, COUNT(sp.id_evento) AS numero_eventi
        FROM sponsor s
        LEFT JOIN sponsorizza sp ON s.id_sponsor = sp.id_sponsor
        GROUP BY s.id_sponsor
        ORDER BY s.nome
    """).fetchall()
    return render_template("sponsor.html", sponsors=rows)


@app.route("/database")
def database():
    return render_template("database.html")


@app.cli.command("reset-db")
def reset_db_command():
    init_db()
    print("Database GestionEventi ricreato correttamente.")


if __name__ == "__main__":
    with app.app_context():
        if not DATABASE.exists():
            init_db()
    app.run(debug=True)
