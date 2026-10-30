<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

define('BASE_PATH', dirname(__DIR__, 2));

$domain = $_SERVER['HTTP_HOST'];

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

$protocol = $isHttps ? 'https://' : 'http://';

if ($domain == 'localhost' || str_contains($domain, '192.168.') || $domain == '127.0.0.1') {
    $projectFolder = 'abuabu/';
} else {
    $projectFolder = '';
}

define('BASE_URL', $protocol . $domain . '/' . $projectFolder);

$host = getenv('DB_HOST') ?: 'localhost';

$username = getenv('DB_USER') ?: 'root';

$password = getenv('DB_PASSWORD') ?: '';

$database = getenv('DB_NAME') ?: 'nosdeluz_db';

$port = (int) (getenv('DB_PORT') ?: 3306);

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database,
    $port
);

//FUNÇÃO AUXILIAR DE EXIBIÇÃO SEGURA DE DADOS
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>