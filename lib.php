<?php
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/classes/logger.php');

/**
 * Fungsi ini dipanggil manual dari config.php atau entry point lain.
 */
function local_http_logger_before_http_headers() {
    \local_http_logger\logger::log_request();
}

function local_http_logger_after_config() {
    // Panggil logger utama untuk semua request
    \local_http_logger\logger::log_request();
}
?>