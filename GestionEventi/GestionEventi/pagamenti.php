<?php require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/header.php';
$pagamenti=fetch_all($pdo,"SELECT p.*, c.nome, c.cognome FROM pagamento p JOIN cliente c ON p.id_cliente=c.id_cliente ORDER BY p.data_pagamento DESC");
$tot=fetch_one($pdo,"SELECT COALESCE(SUM(importo),0) AS totale, COUNT(*) AS numero FROM pagamento WHERE esito='completato'");
?>
<div class="section-title"><h1>Pagamenti</h1><p class="muted">Storico pagamenti effettuati dai clienti.</p></div>
<section class="grid"><div class="stat"><span>Pagamenti completati</span><strong><?=e($tot['numero'])?></strong></div><div class="stat"><span>Totale incassato</span><strong>€ <?=e(number_format((float)$tot['totale'],2,',','.'))?></strong></div></section>
<div class="section-title"><h2>Lista pagamenti</h2></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>Cliente</th><th>Importo</th><th>Metodo</th><th>Esito</th><th>Data</th></tr></thead><tbody><?php foreach($pagamenti as $p): ?><tr><td>#<?=e($p['id_pagamento'])?></td><td><?=e($p['nome'].' '.$p['cognome'])?></td><td>€ <?=e(number_format((float)$p['importo'],2,',','.'))?></td><td><?=e($p['metodo'])?></td><td><span class="pill"><?=e($p['esito'])?></span></td><td><?=e(date('d/m/Y H:i', strtotime($p['data_pagamento'])))?></td></tr><?php endforeach; if(!$pagamenti): ?><tr><td colspan="6" class="empty">Nessun pagamento.</td></tr><?php endif; ?></tbody></table></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
