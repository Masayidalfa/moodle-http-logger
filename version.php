<?php
defined('MOODLE_INTERNAL') || die(); //mencegah file diakse melalu browser

$plugin->component = 'local_requestlogger'; //Nama Plugin
$plugin->version = 2025045600;              //Versi Plugin
$plugin->requires = 2021051700;             //Moodle Minimal Versi 3.11
$plugin->maturity = MATURITY_ALPHA;         //Versi Awal, Mungkin Masih Banyak Bug
$plugin->release = '1.0.0';                 //Major Version
$plugin->settings = true;                   //Dapat diatur secara GUI

?>
