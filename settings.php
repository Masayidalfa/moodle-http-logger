<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_requestlogger', get_string('pluginname', 'local_requestlogger'));

    $settings->add(new admin_setting_configtext(
        'local_requestlogger/redis_host',
        'Redis Host',
        'Alamat IP Redis Server',
        '127.0.0.1'
    ));

    $settings->add(new admin_setting_configtext(
        'local_requestlogger/redis_port',
        'Redis Port',
        'Port koneksi Redis',
        '6379'
    ));

    $settings->add(new admin_setting_configtext(
        'local_requestlogger/redis_channel',
        'Redis Channel',
        'Nama channel publish log',
        'moodle_logs'
    ));

    $ADMIN->add('localplugins', $settings);
}
