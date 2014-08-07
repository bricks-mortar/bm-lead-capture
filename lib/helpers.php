<?php

$config_path = __DIR__ . '/config.json';

/**
 * @param $config
 * @since 1.0
 */
function writeConfig($config) {

    $handle = fopen(__DIR__ . '/../config.json', 'wb');

    if (!$handle) {
        return;
    }

    fwrite($handle, json_encode($config));
}

function getAttachmentPath($url) {
    $file_path = parse_url($url, PHP_URL_PATH);
    $pos = strpos($file_path, 'wp-content');
    $new_path = substr($file_path, $pos+10, strlen($file_path));
    return WP_CONTENT_DIR . $new_path;
}