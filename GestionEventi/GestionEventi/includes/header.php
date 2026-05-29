<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/functions.php';
$current = basename($_SERVER['PHP_SELF']);
$nav = [
    'index.php' => ['label' => 'Dashboard', 'icon' => '⌂'],
    'eventi.php' => ['label' => 'Eventi', 'icon' => '◆'],
    'biglietteria.php' => ['label' => 'Biglietteria', 'icon' => '◈'],
    'clienti.php' => ['label' => 'Clienti', 'icon' => '●'],
    'artisti.php' => ['label' => 'Artisti', 'icon' => '♪'],
    'sponsor.php' => ['label' => 'Sponsor', 'icon' => '✦'],
    'luoghi.php' => ['label' => 'Luoghi', 'icon' => '⌖'],
    'pagamenti.php' => ['label' => 'Pagamenti', 'icon' => '€']
];
$pageTitle = $nav[$current]['label'] ?? 'GestionEventi';
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0e1528">
    <title><?= e($pageTitle) ?> · GestionEventi</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<a class="skip-link" href="#contenuto">Vai al contenuto</a>
<header class="topbar">
    <a class="brand" href="index.php" aria-label="Torna alla dashboard GestionEventi"><span class="brand-mark">GE</span><strong><span>Gestion</span>Eventi</strong></a>
    <button class="menu-toggle" id="menuToggle" aria-label="Apri menu" aria-expanded="false" aria-controls="mainNav">☰</button>
    <nav class="nav" id="mainNav" aria-label="Navigazione principale">
        <?php foreach ($nav as $file => $item): ?>
            <a class="<?= $current === $file ? 'active' : '' ?>" href="<?= e($file) ?>">
                <span class="nav-icon" aria-hidden="true"><?= e($item['icon']) ?></span><?= e($item['label']) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</header>
<main class="container" id="contenuto">
<?php show_flash(); ?>
