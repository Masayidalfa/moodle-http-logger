<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_http_logger', get_string('pluginname', 'local_http_logger'));

    $settings->add(new admin_setting_configtext(
        'local_http_logger/redis_host',
        get_string('redis_host', 'local_http_logger'),
        get_string('redis_host_desc', 'local_http_logger'),
        '127.0.0.1'
    ));

    $settings->add(new admin_setting_configtext(
        'local_http_logger/redis_port',
        get_string('redis_port', 'local_http_logger'),
        get_string('redis_port_desc', 'local_http_logger'),
        '6379'
    ));

    $settings->add(new admin_setting_configtext(
        'local_http_logger/redis_channel',
        get_string('redis_channel', 'local_http_logger'),
        get_string('redis_channel_desc', 'local_http_logger'),
        'http_logs'
    ));

    $ADMIN->add('localplugins', $settings);
}
?>