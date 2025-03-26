<?php

namespace local_requestlogger;

defined('MOODLE_INTERNAL') || die(); // mencegah file diakses langsung

class logger {
    public static function log_request() {
        global $USER;

        // Ambil IP address
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $request_method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $user_id = $USER->id ?? 0;

        //membuat timmestamp dengan format yang mudah dibaca
        $micro = microtime(true);
        $date = date("d/m/Y H:i:s", $micro);
        $milliseconds = sprintf("%03d", ($micro - floor($micro)) * 1000);
        $timestamp = $date . '.' . $milliseconds;

        // Ambil body request berdasarkan method
        $body = null;
        if (in_array($request_method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            // Cek apakah ada data di $_POST
            if (!empty($_POST)) {
                $body = $_POST;
            } else {
                // Jika tidak, coba ambil data mentah
                $rawdata = file_get_contents('php://input');
                // Jika data mentah berupa JSON, coba decode
                $decoded = json_decode($rawdata, true);
                $body = ($decoded !== null) ? $decoded : $rawdata;
            }
        } else {
            // Untuk GET, ambil query string (jika ada)
            $body = $_SERVER['QUERY_STRING'] ?? '';
        }

        // Susun payload sebagai array yang berisi method, URL, dan body message
        $payloadData = array(
            "method" => $request_method,
            "url"    => $url,
            "body"   => $body
        );
        $payload = json_encode($payloadData, JSON_PRETTY_PRINT);

        // Buat objek record log yang akan disimpan ke database
        $record = new \stdClass();
        $record->userid    = $user_id;
        $record->ip        = $ip_address;
        $record->timestamp = $timestamp;
        $record->payload   = $payload;

        // ------------------------------------------------------------------
        // Koneksi ke Redis dan publish data log ke channel "moodle_logs"
        // ------------------------------------------------------------------
        try {
            $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379);

            // Publish log sebagai JSON ke channel "moodle_logs"
            $redis->publish('moodle_logs', json_encode($record));
        } catch (\Exception $e) {
            error_log('Redis error (logger.php): ' . $e->getMessage());
        }
    }
}
