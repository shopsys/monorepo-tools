<?php

/**
 * This file implements rewrite rules for PHP built-in web server.
 *
 * @see http://www.php.net/manual/en/features.commandline.webserver.php
 * @see Symfony/Bundle/FrameworkBundle/Resources/config/router_dev.php
 */

// Workaround https://bugs.php.net/64566
if (ini_get('auto_prepend_file') && !in_array(realpath(ini_get('auto_prepend_file')), get_included_files(), true)) {
    require ini_get('auto_prepend_file');
}

if (is_file($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $_SERVER['SCRIPT_NAME'])) {
    return false;
}

$_SERVER = array_merge($_SERVER, $_ENV);
$_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'index.php';

// Since we are rewriting to index.php, adjust SCRIPT_NAME and PHP_SELF accordingly
$_SERVER['SCRIPT_NAME'] = DIRECTORY_SEPARATOR . 'index.php';
$_SERVER['PHP_SELF'] = DIRECTORY_SEPARATOR . 'index.php';

require 'index.php';

error_log(sprintf('%s:%d [%d]: %s', $_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT'], http_response_code(), $_SERVER['REQUEST_URI']), 4);
