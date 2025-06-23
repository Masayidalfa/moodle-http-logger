<?php
namespace local_http_logger;

defined('MOODLE_INTERNAL') || die();

class logger {
    public static function log_request() {
        $timestamp = time();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $url = $_SERVER['REQUEST_URI'] ?? '';
        $payload = '';

        // Ambil payload
        if ($method === 'POST') {
            $payload = file_get_contents('php://input');
        } elseif ($method === 'GET') {
            $payload = json_encode($_GET);
        }

        // Status code tidak bisa didapat langsung
        $status_code = null;

        $logdata = [
            'timestamp'    => $timestamp,
            'ip_address'   => $ip,
            'method'       => $method,
            'url'          => $url,
            'status_code'  => $status_code,
            'payload'      => $payload
        ];

        self::publish_to_redis($logdata);
    }

    private static function publish_to_redis(array $data) {
    try {
        $redis = new \Redis();
        $host = get_config('local_http_logger', 'redis_host') ?: '127.0.0.1';
        $port = get_config('local_http_logger', 'redis_port') ?: 6379;
        $channel = get_config('local_http_logger', 'redis_channel') ?: 'http_logs';
        $dedupTTL = 5;

        $redis->connect($host, (int)$port);

        // Salin log tanpa timestamp untuk membuat hash isi log
        $logForHash = $data;
        unset($logForHash['timestamp']);

        // Sort key agar hasil hash konsisten
        ksort($logForHash);
        $logString = json_encode($logForHash);
        $logHash = hash('sha256', $logString);

        // Cek apakah hash sudah ada (duplikat)
        if (!$redis->exists($logHash)) {
            // Simpan hash ke Redis dengan TTL
            $redis->setex($logHash, $dedupTTL, 1);
            // Kirim log ke channel Redis
            $redis->publish($channel, json_encode($data));
        } else {
            
        }
    } catch (\Exception $e) {
        debugging('Redis publish error: ' . $e->getMessage(), DEBUG_DEVELOPER);
    }
}


}
?>