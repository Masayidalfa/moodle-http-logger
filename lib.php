<?php 
defined('MOODLE_INTERNAL') || die(); // mencegah file dibuka/diakses langsung

require_once(__DIR__ . '/classes/logger.php');

/**
 * Fungsi ini dipanggil setiap kali halaman dimuat.
 * Mencatat request HTTP dan menyisipkan modul JavaScript untuk menangkap submit event.
 */
function local_requestlogger_extend_navigation(\global_navigation $navigation) {
    local_requestlogger_log_page_load();
    local_requestlogger_inject_formlogger();
}

/**
 * Mencatat setiap request halaman.
 */
function local_requestlogger_log_page_load() {
    \local_requestlogger\logger::log_request();
}

/**
 * Menyisipkan modul JavaScript formlogger untuk menangkap submit event.
 */
function local_requestlogger_inject_formlogger() {
    global $PAGE;
    $PAGE->requires->js_call_amd('local_requestlogger/formlogger', 'init');
}

/**
 * Menyisipkan footer untuk halaman login.
 */
function local_requestlogger_before_footer() {
    global $PAGE;

    if ($PAGE->pagetype === 'login-index') {
        $renderer = $PAGE->get_renderer('local_requestlogger');
        echo $renderer->render_footer();
    }
}
/** 
* Mendaftarkan hook
*/
function local_requestlogger_register_hooks() {
    global $CFG;
    if ($CFG->version >= 2021051700) { // Moodle 3.11 ke atas
        $CFG->additionalhtmlfooter .= local_requestlogger_before_footer();
    }
}


?>
