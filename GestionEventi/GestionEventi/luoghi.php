<?php require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $stmt=$pdo->prepare('INSERT INTO luogo (nome, indirizzo, citta, capienza) VALUES (?,?,?,?)');
    $stmt->execute([$_POST['nome'], $_POST['indirizzo'], $_POST['citta'], (int)$_POST['capienza']]);
    flash('Luogo registrato correttamente.'); redirect('luoghi.php');
}
$luoghi=fetch_all($pdo,'SELECT * FROM luogo ORDER BY citta, nome');
?>
<div class="section-title"><h1>Luoghi</h1><p class="muted">Anagrafica dei luoghi in cui si svolgono gli eventi.</p></div>
<section class="grid-2"><div class="card"><h2>Nuovo luogo</h2><form class="form" method="post"><div><label>Nome</label><input name="nome" required></div><div><label>Città</label><input name="citta" required></div><div class="full"><label>Indirizzo</label><input name="indirizzo" required></div><div><label>Capienza</label><input type="number" min="1" name="capienza" required></div><div class="full"><button class="btn">Salva luogo</button></div></form></div>
<div class="table-wrap"><table><thead><tr><th>Nome</th><th>Indirizzo</th><th>Città</th><th>Capienza</th></tr></thead><tbody><?php foreach($luoghi as $l): ?><tr><td><?=e($l['nome'])?></td><td><?=e($l['indirizzo'])?></td><td><?=e($l['citta'])?></td><td><?=e($l['capienza'])?></td></tr><?php endforeach; if(!$luoghi): ?><tr><td colspan="4" class="empty">Nessun luogo.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
