<?php 
defined('MOODLE_INTERNAL') || die(); // Mencegah akses langsung

require_once(__DIR__ . '/classes/logger.php');

/**
 * Fungsi ini dipanggil saat halaman dimuat.
 * Mencatat request dan menyisipkan JavaScript untuk form logger.
 *
 * @param \global_navigation $navigation Navigasi global Moodle
 */
function local_requestlogger_extend_navigation(\global_navigation $navigation) {
    local_requestlogger_log_page_load();
    local_requestlogger_inject_formlogger();
}

/**
 * Mencatat setiap permintaan halaman menggunakan logger class.
 */
function local_requestlogger_log_page_load() {
    \local_requestlogger\logger::log_request();
}

/**
 * Menyisipkan modul JavaScript formlogger via AMD module.
 */
function local_requestlogger_inject_formlogger() {
    global $PAGE;
    $PAGE->requires->js_call_amd('local_requestlogger/formlogger', 'init');
}

/**
 * Menyisipkan skrip tambahan di footer halaman login (khusus login-index).
 */
function local_requestlogger_before_footer() {
    global $PAGE;

    if ($PAGE->pagetype === 'login-index') {
        $renderer = $PAGE->get_renderer('local_requestlogger');
        return $renderer->render_footer();
    }

    return '';
}

/**
 * Mendaftarkan hook tambahan untuk HTML footer (khusus Moodle 3.11 ke atas).
 */
function local_requestlogger_register_hooks() {
    global $CFG;
    if ($CFG->version >= 2021051700) {
        $CFG->additionalhtmlfooter .= local_requestlogger_before_footer();
    }
}

?>