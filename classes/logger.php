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
            $rawdata = file_get_contents('php://input');
            $decoded = json_decode($rawdata, true);

            // Gunakan POST jika tersedia, fallback ke data mentah
            $body = !empty($_POST) ? $_POST : ($decoded ?? $rawdata);
        } else {
            $body = $_SERVER['QUERY_STRING'] ?? '';
        }

        // Susun payload
        $payloadData = [
            'method' => $request_method,
            'url'    => $url,
            'body'   => $body
        ];

        $record = (object)[
            'userid'    => $user_id,
            'ip'        => $ip_address,
            'timestamp' => $timestamp,
            'payload'   => json_encode($payloadData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ];

        // Kirim ke Redis
        try {
            $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->publish('moodle_logs', json_encode($record));
        } catch (\Throwable $e) {
            // Log error internal tanpa tampil ke user
            error_log('Redis error (logger.php): ' . $e->getMessage());
        }
    }
}
