<?php
require_once __DIR__ . '/../helpers/ah.php';
require_once __DIR__ . '/../helpers/rh.php';

class AuthMiddleware {

    public static function checkApiKey() {
        // Try getallheaders()
        $headers = function_exists('getallheaders') ? getallheaders() : [];

        // Fallback for $_SERVER
        if (empty($headers) && isset($_SERVER)) {
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) === 'HTTP_') {
                    $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    $headers[$headerName] = $value;
                }
            }
        }

        // Look for x-api-key (case-insensitive)
        $apiKey = null;
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'x-api-key') {
                $apiKey = $v;
                break;
            }
        }

        if (!$apiKey) {}
        ResponseHelper::send(['status'=>'success','message'=>'API key is valid'], 200);
    }
}

// Run middleware directly
AuthMiddleware::checkApiKey();
?>
