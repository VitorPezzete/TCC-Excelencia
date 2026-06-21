<?php
// Arquivo temporário de debug - REMOVER APÓS USO
$token = $_GET['t'] ?? '';
if ($token !== 'excelencia2026') {
    die('Acesso negado.');
}

$action = $_GET['a'] ?? 'status';

if ($action === 'opcache') {
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo json_encode(['opcache' => 'cleared']);
    } else {
        echo json_encode(['opcache' => 'not_available']);
    }
    exit;
}

// Ler/escrever setting diretamente
$pdo = new PDO(
    'mysql:host=' . env_get('DB_HOST', '127.0.0.1') . ';dbname=' . env_get('DB_DATABASE', ''),
    env_get('DB_USERNAME', ''),
    env_get('DB_PASSWORD', '')
);

if ($action === 'set') {
    $val = $_GET['v'] ?? '1';
    $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`, `created_at`, `updated_at`) VALUES ('is_store_open', ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE `value`=?, `updated_at`=NOW()");
    $stmt->execute([$val, $val]);
    echo json_encode(['action' => 'set', 'value' => $val, 'rows' => $stmt->rowCount()]);
} else {
    $stmt = $pdo->query("SELECT * FROM settings WHERE `key` = 'is_store_open'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['db_value' => $row]);
}

function env_get($key, $default = '') {
    $env = parse_ini_file(dirname(__DIR__) . '/.env');
    return $env[$key] ?? $default;
}
