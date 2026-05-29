<?php require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/header.php';
$counts = [];
foreach (['evento','biglietto','cliente','artista','sponsor','luogo','pagamento'] as $t) {
    $counts[$t] = (int)fetch_one($pdo, "SELECT COUNT(*) AS c FROM $t")['c'];
}
$prossimi = fetch_all($pdo, "SELECT e.*, l.nome AS luogo, l.citta FROM evento e JOIN luogo l ON e.id_luogo=l.id_luogo ORDER BY e.data_evento ASC, e.ora ASC LIMIT 5");
$vendite = fetch_all($pdo, "SELECT e.titolo, COUNT(b.id_biglietto) AS biglietti, COALESCE(SUM(b.prezzo),0) AS incasso FROM evento e LEFT JOIN biglietto b ON e.id_evento=b.id_evento GROUP BY e.id_evento ORDER BY incasso DESC LIMIT 5");
$totali = fetch_one($pdo, "SELECT COALESCE(SUM(importo),0) AS incasso, COUNT(*) AS pagamenti FROM pagamento WHERE esito='completato'");
$ultimoPagamento = fetch_one($pdo, "SELECT data_pagamento FROM pagamento ORDER BY data_pagamento DESC LIMIT 1");
$maxIncasso = 0;
foreach ($vendite as $r) { $maxIncasso = max($maxIncasso, (float)$r['incasso']); }
?>
<section class="hero">
  <div class="panel hero-main">
    <span class="badge">Sistema gestione eventi</span>
    <h1>Organizza eventi memorabili con una dashboard chiara e moderna.</h1>
    <p class="lead">GestionEventi coordina eventi musicali, spettacoli e concerti: anagrafiche, luoghi, artisti, sponsor, biglietteria e pagamenti rimangono collegati allo schema relazionale del database.</p>
    <div class="actions">
      <a class="btn" href="eventi.php">Registra evento</a>
      <a class="btn secondary" href="biglietteria.php">Vendi biglietto</a>
    </div>
    <div class="metric-row">
      <div class="mini-metric"><span>Incasso completato</span><strong>€ <?= e(number_format((float)$totali['incasso'],2,',','.')) ?></strong></div>
      <div class="mini-metric"><span>Pagamenti completati</span><strong><?= e($totali['pagamenti']) ?></strong></div>
    </div>
  </div>
  <aside class="panel quick-card">
    <p class="eyebrow">Azioni rapide</p>
    <h2>Operazioni frequenti</h2>
    <a href="luoghi.php">Aggiungi una location <span>→</span></a>
    <a href="clienti.php">Registra un cliente <span>→</span></a>
    <a href="artisti.php">Inserisci un artista <span>→</span></a>
    <a href="sponsor.php">Collega uno sponsor <span>→</span></a>
    <div class="soft-divider"></div>
    <p class="muted">Ultimo pagamento: <strong><?= $ultimoPagamento ? e(date('d/m/Y H:i', strtotime($ultimoPagamento['data_pagamento']))) : 'nessuno' ?></strong></p>
  </aside>
</section>

<section class="grid">
<?php foreach ([ 'evento'=>'Eventi', 'biglietto'=>'Biglietti', 'cliente'=>'Clienti', 'artista'=>'Artisti', 'sponsor'=>'Sponsor', 'luogo'=>'Luoghi', 'pagamento'=>'Pagamenti' ] as $key=>$label): ?>
  <div class="stat"><span><?= e($label) ?></span><strong><?= e($counts[$key]) ?></strong><small>Totale registrato</small></div>
<?php endforeach; ?>
</section>

<section class="grid-2">
  <div>
    <div class="section-title"><h2>Prossimi eventi</h2><p class="muted">Calendario operativo</p></div>
    <div class="panel timeline">
      <?php if (!$prossimi): ?><p class="empty">Nessun evento registrato.</p><?php endif; ?>
      <?php foreach($prossimi as $r): ?>
        <article class="timeline-item">
          <div class="timeline-date"><?= e(date('d M', strtotime($r['data_evento']))) ?><br><span class="muted"><?= e(substr($r['ora'],0,5)) ?></span></div>
          <div><strong><?= e($r['titolo']) ?></strong><br><span class="pill"><?= e($r['genere']) ?></span><p class="muted"><?= e($r['luogo'].' · '.$r['citta']) ?></p></div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
  <div>
    <div class="section-title"><h2>Andamento vendite</h2><p class="muted">Top eventi per incasso</p></div>
    <div class="panel chart-list">
      <?php if (!$vendite): ?><p class="empty">Nessuna vendita registrata.</p><?php endif; ?>
      <?php foreach($vendite as $r): $perc = $maxIncasso > 0 ? max(4, round(((float)$r['incasso'] / $maxIncasso) * 100)) : 4; ?>
        <div class="chart-item">
          <strong><?= e($r['titolo']) ?></strong>
          <div class="bar" aria-label="Incasso relativo"><span style="width: <?= e($perc) ?>%"></span></div>
          <span>€ <?= e(number_format((float)$r['incasso'],2,',','.')) ?></span>
          <small class="muted"><?= e($r['biglietti']) ?> biglietti</small>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
