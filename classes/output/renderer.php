<?php
namespace local_requestlogger\output;
use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    public function render_footer() {
        return "<script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('⏳ Menunggu RequireJS...');
                require(['local_requestlogger/formlogger'], function(module) {
                    console.log('✅ Plugin formlogger otomatis dimuat di halaman login...');
                    module.init();
                }, function(err) {
                    console.error('❌ Plugin tidak bisa dimuat:', err);
                });
            });
        </script>";
    }
}
?>
