<?php
require_once('../../config.php');
defined('MOODLE_INTERNAL') || die();

// Hanya terima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Deteksi Content-Type dan ambil data dari request
$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $rawdata = file_get_contents('php://input');
    $data = json_decode($rawdata, true) ?: $_POST;
} elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false || strpos($contentType, 'multipart/form-data') !== false) {
    $data = $_POST;
} else {
    parse_str(file_get_contents("php://input"), $data);
}

// Ambil informasi dasar request
$userid = isset($data['userid']) ? intval($data['userid']) : 0;
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$url = $_SERVER['HTTP_REFERER'] ?? 'unknown';
$request_method = $_SERVER['REQUEST_METHOD'] ?? 'unknown';

// Buat timestamp dengan format "dd/mm/YYYY HH:ii:ss.mmm"
$micro = microtime(true);
$date = date("d/m/Y H:i:s", $micro);
$milliseconds = sprintf("%03d", ($micro - floor($micro)) * 1000);
$timestamp = $date . '.' . $milliseconds;

// Susun payload sebagai array
$payloadData = [
    "method" => $request_method,
    "url"    => $url,
    "body"   => $data
];
$payload = json_encode($payloadData, JSON_PRETTY_PRINT);

// Buat objek record untuk disimpan
$record = new stdClass();
$record->userid    = $userid;
$record->ip        = $ip;
$record->timestamp = $timestamp;
$record->payload   = $payload;

// Kirim respon JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);

// Koneksi ke Redis dan publish data log ke channel "moodle_logs"
try {
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->publish('moodle_logs', json_encode($record));
} catch (\Exception $e) {
    error_log('Redis error (log.php): ' . $e->getMessage());
}
