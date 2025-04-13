<?php
namespace local_requestlogger\output;

use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    /**
     * Menyisipkan JavaScript secara langsung untuk memuat modul AMD formlogger.
     * Fungsi ini digunakan khusus pada halaman login.
     *
     * @return string HTML <script> untuk inisialisasi formlogger
     */
    public function render_footer() {
        return "<script>
            document.addEventListener('DOMContentLoaded', function() {
                require(['local_requestlogger/formlogger'], function(module) {
                    module.init();
                });
            });
        </script>";
    }
}
