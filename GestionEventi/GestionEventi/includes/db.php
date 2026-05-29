<?php
// Configurazione database - modificare se necessario.
$host = '127.0.0.1';
$dbname = 'gestione_eventi';
$user = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die('<h1>Errore connessione database</h1><p>Controlla config in <code>includes/db.php</code> e importa <code>database/dump1.sql</code>.</p><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}
?>
