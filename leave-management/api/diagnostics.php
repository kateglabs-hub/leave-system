<?php
// Diagnostic page for debugging frontend issues
header('Content-Type: application/json');

$diagnostics = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server_api' => php_sapi_name(),
    'environment' => [
        'DB_HOST' => getenv('DB_HOST') ?: 'not set',
        'DB_NAME' => getenv('DB_NAME') ?: 'not set',
        'DB_USER' => getenv('DB_USER') ?: 'not set',
        'APP_ENV' => getenv('APP_ENV') ?: 'not set',
    ],
    'file_system' => [
        'public_exists' => is_dir(__DIR__ . '/public'),
        'api_exists' => is_file(__DIR__ . '/api/index.php'),
        'classes_exist' => is_dir(__DIR__ . '/classes'),
        'config_exists' => is_dir(__DIR__ . '/config'),
    ],
    'database' => [
        'connection_test' => 'Testing...'
    ],
    'server_info' => [
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'unknown',
        'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'] ?? 'unknown',
        'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
    ],
];

// Test database connection
try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $db_name = getenv('DB_NAME') ?: 'leave_management';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    
    $conn = new PDO(
        "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_TIMEOUT => 5]
    );
    $diagnostics['database']['connection_test'] = 'SUCCESS';
    $diagnostics['database']['tables'] = [];
    
    $stmt = $conn->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $diagnostics['database']['tables'][] = $row[0];
    }
} catch (Exception $e) {
    $diagnostics['database']['connection_test'] = 'FAILED: ' . $e->getMessage();
}

echo json_encode($diagnostics, JSON_PRETTY_PRINT);
?>
