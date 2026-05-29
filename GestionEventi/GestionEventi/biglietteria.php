<?php require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        $pdo->beginTransaction();
        $stmt=$pdo->prepare('INSERT INTO pagamento (id_cliente,importo,metodo,esito,data_pagamento) VALUES (?,?,?,?,NOW())');
        $stmt->execute([(int)$_POST['id_cliente'], (float)$_POST['prezzo'], $_POST['metodo'], $_POST['esito']]);
        $pid=(int)$pdo->lastInsertId();
        $stmt=$pdo->prepare('INSERT INTO biglietto (id_cliente,id_pagamento,id_evento,prezzo,tipo,posto,stato) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([(int)$_POST['id_cliente'], $pid, (int)$_POST['id_evento'], (float)$_POST['prezzo'], $_POST['tipo'] ?: null, $_POST['posto'] ?: null, $_POST['stato']]);
        $pdo->commit(); flash('Biglietto venduto e pagamento registrato.');
    } catch(Exception $e) { $pdo->rollBack(); flash('Errore: '.$e->getMessage(),'error'); }
    redirect('biglietteria.php');
}
$clienti=fetch_all($pdo,'SELECT * FROM cliente ORDER BY cognome, nome');
$eventi=fetch_all($pdo,"SELECT e.*, l.nome AS luogo, l.citta FROM evento e JOIN luogo l ON e.id_luogo=l.id_luogo ORDER BY e.data_evento ASC");
$biglietti=fetch_all($pdo,"SELECT b.*, c.nome, c.cognome, e.titolo, e.data_evento, p.metodo, p.esito FROM biglietto b JOIN cliente c ON b.id_cliente=c.id_cliente JOIN evento e ON b.id_evento=e.id_evento JOIN pagamento p ON b.id_pagamento=p.id_pagamento ORDER BY b.id_biglietto DESC");
?>
<div class="section-title"><h1>Biglietteria</h1><p class="muted">Acquisto biglietti con creazione automatica del pagamento collegato.</p></div>
<section class="card"><h2>Vendi biglietto</h2>
<?php if(!$clienti || !$eventi): ?><p class="alert alert-error">Per vendere un biglietto servono almeno un cliente e un evento registrati.</p><?php endif; ?>
<form class="form" method="post"><div><label>Cliente</label><select name="id_cliente" required><option value="">Seleziona cliente</option><?php foreach($clienti as $c): ?><option value="<?=e($c['id_cliente'])?>"><?=e($c['cognome'].' '.$c['nome'].' - '.$c['email'])?></option><?php endforeach; ?></select></div><div><label>Evento</label><select name="id_evento" required><option value="">Seleziona evento</option><?php foreach($eventi as $ev): ?><option value="<?=e($ev['id_evento'])?>"><?=e($ev['titolo'].' - '.date('d/m/Y', strtotime($ev['data_evento'])).' - '.$ev['luogo'])?></option><?php endforeach; ?></select></div><div><label>Prezzo</label><input type="number" step="0.01" min="0" name="prezzo" required></div><div><label>Tipo</label><select name="tipo"><option>Intero</option><option>Ridotto</option><option>VIP</option><option>Backstage</option><option>Tribuna</option></select></div><div><label>Posto</label><input name="posto" placeholder="Es. A12, Platea 3"></div><div><label>Stato biglietto</label><select name="stato"><option value="valido">Valido</option><option value="annullato">Annullato</option><option value="usato">Usato</option></select></div><div><label>Metodo pagamento</label><select name="metodo"><option>Carta</option><option>PayPal</option><option>Bonifico</option><option>Contanti</option></select></div><div><label>Esito pagamento</label><select name="esito"><option>completato</option><option>in attesa</option><option>rifiutato</option></select></div><div class="full"><button class="btn ok" <?= (!$clienti || !$eventi) ? 'disabled' : '' ?>>Conferma vendita</button></div></form></section>
<div class="section-title"><h2>Biglietti venduti</h2></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>Cliente</th><th>Evento</th><th>Prezzo</th><th>Tipo / posto</th><th>Pagamento</th><th>Stato</th></tr></thead><tbody><?php foreach($biglietti as $b): ?><tr><td>#<?=e($b['id_biglietto'])?></td><td><?=e($b['nome'].' '.$b['cognome'])?></td><td><?=e($b['titolo'])?><br><span class="muted"><?=e(date('d/m/Y', strtotime($b['data_evento'])))?></span></td><td>€ <?=e(number_format((float)$b['prezzo'],2,',','.'))?></td><td><?=e($b['tipo'])?><br><span class="muted"><?=e($b['posto'])?></span></td><td><?=e($b['metodo'])?><br><span class="pill"><?=e($b['esito'])?></span></td><td><?=e($b['stato'])?></td></tr><?php endforeach; if(!$biglietti): ?><tr><td colspan="7" class="empty">Nessun biglietto venduto.</td></tr><?php endif; ?></tbody></table></div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
