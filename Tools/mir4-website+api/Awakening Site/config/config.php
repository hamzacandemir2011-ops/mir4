<?php
// Default (placeholder) settings. Do NOT put real credentials in this file.
// Copy config.local.example.php to config.local.php and set your real values there;
// config.local.php is git-ignored so secrets never end up in the repository.
$host = '127.0.0.1'; // The host of your database
$db_mm_game_db = 'mm_game_db_release'; // The name of your database
$db_mm_user_db = 'mm_user_db'; // The name of your database
$user = 'root'; // The database user
$pass = ''; // The password of the database user
$charset = 'utf8mb4'; // The charset you wish to use

// PHPMailer settings
$mailHost = 'smtp.example.com'; // SMTP server
$mailPort = 465; // SMTP port
$mailUsername = ''; // SMTP username
$mailPassword = ''; // SMTP password
$mailFrom = 'noreply@example.com'; // From email address
$mailFromName = 'MIR4 LOMCN Register'; // From name
$mailIsSMTP = true; // Whether to use SMTP

// hCaptcha secret key (https://dashboard.hcaptcha.com)
$hcaptchaSecret = '';

if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

$dsn_game = "mysql:host=$host;dbname=$db_mm_game_db;charset=$charset";
$dsn_user = "mysql:host=$host;dbname=$db_mm_user_db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo_game = new PDO($dsn_game, $user, $pass, $options);
} catch (\PDOException $e) {
     error_log('Database connection failed: ' . $e->getMessage());
     die("Não foi possível se conectar ao banco de dados.");
}

try {
     $pdo_user = new PDO($dsn_user, $user, $pass, $options);
} catch (\PDOException $e) {
     error_log('Database connection failed: ' . $e->getMessage());
     die("Não foi possível se conectar ao banco de dados.");
}

// Verifies an hCaptcha response token server-side. Returns true only on a confirmed success.
function verify_hcaptcha($secret, $token)
{
    if (empty($secret) || empty($token)) {
        return false;
    }
    $context = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]),
        'timeout' => 10,
    ]]);
    $verifyResponse = @file_get_contents('https://hcaptcha.com/siteverify', false, $context);
    if ($verifyResponse === false) {
        return false;
    }
    $responseData = json_decode($verifyResponse);
    return !empty($responseData->success);
}
