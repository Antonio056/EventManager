<?php require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        $pdo->beginTransaction();
        $stmt=$pdo->prepare('INSERT INTO evento (id_luogo,titolo,data_evento,ora,descrizione,genere) VALUES (?,?,?,?,?,?)');
        $stmt->execute([(int)$_POST['id_luogo'],$_POST['titolo'],$_POST['data_evento'],$_POST['ora'],$_POST['descrizione'] ?: null,$_POST['genere'] ?: null]);
        $eventId=(int)$pdo->lastInsertId();
        $stmtA=$pdo->prepare('INSERT INTO evento_artista (id_evento,id_artista) VALUES (?,?)');
        foreach($_POST['artisti'] ?? [] as $idA) $stmtA->execute([$eventId,(int)$idA]);
        $stmtS=$pdo->prepare('INSERT INTO evento_sponsor (id_evento,id_sponsor) VALUES (?,?)');
        foreach($_POST['sponsor'] ?? [] as $idS) $stmtS->execute([$eventId,(int)$idS]);
        $pdo->commit(); flash('Evento registrato correttamente.');
    } catch(Exception $e){ $pdo->rollBack(); flash('Errore: '.$e->getMessage(),'error'); }
    redirect('eventi.php');
}
$luoghi=fetch_all($pdo,'SELECT * FROM luogo ORDER BY nome');
$artisti=fetch_all($pdo,'SELECT * FROM artista ORDER BY nome_arte');
$sponsors=fetch_all($pdo,'SELECT * FROM sponsor ORDER BY nome');
$eventi=fetch_all($pdo,"SELECT e.*, l.nome AS luogo, l.citta, l.indirizzo, l.capienza,
    GROUP_CONCAT(DISTINCT a.nome_arte ORDER BY a.nome_arte SEPARATOR ', ') AS artisti,
    GROUP_CONCAT(DISTINCT s.nome ORDER BY s.nome SEPARATOR ', ') AS sponsor
    FROM evento e JOIN luogo l ON e.id_luogo=l.id_luogo
    LEFT JOIN evento_artista ea ON e.id_evento=ea.id_evento LEFT JOIN artista a ON ea.id_artista=a.id_artista
    LEFT JOIN evento_sponsor es ON e.id_evento=es.id_evento LEFT JOIN sponsor s ON es.id_sponsor=s.id_sponsor
    GROUP BY e.id_evento ORDER BY e.data_evento DESC, e.ora DESC");
?>
<div class="section-title"><h1>Eventi</h1><p class="muted">Registra data, ora, luogo, descrizione, genere, artisti e sponsor.</p></div>
<section class="card"><h2>Nuovo evento</h2>
<?php if(!$luoghi): ?><p class="alert alert-error">Prima di creare un evento devi inserire almeno un luogo.</p><?php endif; ?>
<form class="form" method="post"><div><label>Titolo</label><input name="titolo" required></div><div><label>Genere</label><input name="genere" placeholder="Concerto, musical, spettacolo..."></div><div><label>Data</label><input type="date" name="data_evento" required></div><div><label>Ora</label><input type="time" name="ora" required></div><div class="full"><label>Luogo</label><select name="id_luogo" required><option value="">Seleziona luogo</option><?php foreach($luoghi as $l): ?><option value="<?=e($l['id_luogo'])?>"><?=e($l['nome'].' - '.$l['citta'].' (capienza '.$l['capienza'].')')?></option><?php endforeach; ?></select></div><div class="full"><label>Descrizione evento e dettagli organizzativi</label><textarea name="descrizione" placeholder="Descrizione dell'evento, note sul luogo, accessi, programma..."></textarea></div><div><label>Artisti coinvolti</label><select name="artisti[]" multiple size="5"><?php foreach($artisti as $a): ?><option value="<?=e($a['id_artista'])?>"><?=e($a['nome_arte'])?></option><?php endforeach; ?></select></div><div><label>Sponsor</label><select name="sponsor[]" multiple size="5"><?php foreach($sponsors as $s): ?><option value="<?=e($s['id_sponsor'])?>"><?=e($s['nome'])?></option><?php endforeach; ?></select></div><div class="full"><button class="btn" <?= !$luoghi ? 'disabled' : '' ?>>Registra evento</button></div></form></section>
<div class="section-title"><h2>Eventi registrati</h2></div><div class="table-wrap"><table><thead><tr><th>Evento</th><th>Data e ora</th><th>Luogo</th><th>Artisti</th><th>Sponsor</th></tr></thead><tbody><?php foreach($eventi as $ev): ?><tr><td><strong><?=e($ev['titolo'])?></strong><br><span class="pill"><?=e($ev['genere'])?></span><p class="muted"><?=e($ev['descrizione'])?></p></td><td><?=e(date('d/m/Y', strtotime($ev['data_evento'])))?><br><?=e(substr($ev['ora'],0,5))?></td><td><?=e($ev['luogo'])?><br><span class="muted"><?=e($ev['indirizzo'].' - '.$ev['citta'])?><br>Capienza: <?=e($ev['capienza'])?></span></td><td><?=e($ev['artisti'] ?: '—')?></td><td><?=e($ev['sponsor'] ?: '—')?></td></tr><?php endforeach; if(!$eventi): ?><tr><td colspan="5" class="empty">Nessun evento.</td></tr><?php endif; ?></tbody></table></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
