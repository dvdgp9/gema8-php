<?php
/**
 * Diagnóstico de Gema8
 * ELIMINAR DESPUÉS DE USAR
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== GEMA8 DIAGNÓSTICO ===\n\n";

// 1. PHP Version
echo "1. PHP Version: " . PHP_VERSION . "\n";

// 2. Cargar config
define('GEMA8', true);
define('ROOT_PATH', dirname(__DIR__));

echo "2. ROOT_PATH: " . ROOT_PATH . "\n";

// 3. Config existe?
$configPath = ROOT_PATH . '/config/config.php';
echo "3. Config existe: " . (file_exists($configPath) ? 'SÍ' : 'NO') . "\n";

if (file_exists($configPath)) {
    require_once $configPath;
    
    // 4. Variables definidas
    echo "4. BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'NO DEFINIDA') . "\n";
    echo "5. ENV: " . (defined('ENV') ? ENV : 'NO DEFINIDA') . "\n";
    echo "6. GEMINI_API_KEY: " . (defined('GEMINI_API_KEY') ? 
        (GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY' ? 'NO CONFIGURADA (valor por defecto)' : 
        'Configurada (' . strlen(GEMINI_API_KEY) . ' caracteres)') : 
        'NO DEFINIDA') . "\n";
    
    // 7. DB Config
    echo "7. DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NO DEFINIDA') . "\n";
    echo "8. DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NO DEFINIDA') . "\n";
}

// 8. cURL disponible?
echo "\n=== EXTENSIONES ===\n";
echo "9. cURL: " . (function_exists('curl_init') ? 'SÍ' : 'NO') . "\n";
echo "10. PDO: " . (class_exists('PDO') ? 'SÍ' : 'NO') . "\n";
echo "11. PDO MySQL: " . (in_array('mysql', PDO::getAvailableDrivers()) ? 'SÍ' : 'NO') . "\n";
echo "12. JSON: " . (function_exists('json_encode') ? 'SÍ' : 'NO') . "\n";

// 9. Test conexión DB
echo "\n=== DATABASE ===\n";
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        echo "13. Conexión DB: OK\n";
        
        // Verificar tablas
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "14. Tablas: " . implode(', ', $tables) . "\n";
    } catch (PDOException $e) {
        echo "13. Conexión DB: ERROR - " . $e->getMessage() . "\n";
    }
}

// 10. Test Gemini API
echo "\n=== GEMINI API ===\n";
if (defined('GEMINI_API_KEY') && GEMINI_API_KEY !== 'YOUR_GEMINI_API_KEY') {
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . GEMINI_API_KEY;
    
    $data = [
        'contents' => [
            ['parts' => [['text' => 'Say "Hello, Gema8 is working!" in exactly those words.']]]
        ]
    ];
    
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "15. cURL Error: " . $error . "\n";
    } else {
        echo "15. HTTP Code: " . $httpCode . "\n";
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                echo "16. Gemini Response: " . $result['candidates'][0]['content']['parts'][0]['text'] . "\n";
                echo "\n✅ GEMINI FUNCIONA CORRECTAMENTE\n";
            } else {
                echo "16. Respuesta inesperada: " . substr($response, 0, 500) . "\n";
            }
        } else {
            echo "16. Error Response: " . substr($response, 0, 500) . "\n";
        }
    }
} else {
    echo "15. GEMINI_API_KEY no configurada - No se puede probar\n";
}

echo "\n=== FIN DIAGNÓSTICO ===\n";
echo "\n⚠️  ELIMINA ESTE ARCHIVO (diagnose.php) DESPUÉS DE USARLO\n";
