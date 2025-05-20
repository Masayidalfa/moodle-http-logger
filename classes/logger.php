<?php

namespace local_requestlogger;

defined('MOODLE_INTERNAL') || die(); // Cegah akses langsung ke file

class logger {
    /**
     * Mencatat data request saat halaman dimuat.
     * Data dikirim ke Redis dalam format JSON.
     */
    public static function log_request() {
        global $USER;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return; // Hindari duplikasi, karena POST ditangani oleh log.php via JS
        }

        // Informasi dasar
        $ip_address      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $request_method  = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $url             = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $user_id         = $USER->id ?? 0;

        // Buat timestamp presisi milidetik
        $micro      = microtime(true);
        $datetime   = date("d/m/Y H:i:s", (int)$micro);
        $millisec   = sprintf("%03d", ($micro - floor($micro)) * 1000);
        $timestamp  = "{$datetime}.{$millisec}";

        // Ambil body sesuai metode HTTP
        $body = null;
        if (in_array($request_method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $body = file_get_contents('php://input');
            if (empty($body)) {
                $body = json_encode($_POST);
            }
        } else {
            $body = urldecode($_SERVER['QUERY_STRING'] ?? '');
        }


        // Susun payload
        $payloadData = [
            'method' => $request_method,
            'url'    => $url,
            'body'   => $body
        ];

        $record = new \stdClass();
        $record->user_id       = $user_id;
        $record->ip_address    = $ip_address;
        $record->timestamp     = $timestamp;
        $record->payloadData   = $payloadData;

        // Kirim ke Redis
        try {
            $host = get_config('local_requestlogger', 'redis_host') ?: '127.0.0.1';
            $port = get_config('local_requestlogger', 'redis_port') ?: 6379;
            $channel = get_config('local_requestlogger', 'redis_channel') ?: 'moodle_logs';
            
            $redis = new \Redis();
            $redis->connect($host, (int)$port);
            $redis->publish($channel, json_encode($record));
        } catch (\Throwable $e) {
            // Log error internal tanpa tampil ke user
            error_log('Redis error (logger.php): ' . $e->getMessage());
        }
    }
}

?>