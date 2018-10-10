<?php
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-after: 300');
    header('Expires: Thu, 01 Dec 1994 16:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
    header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');

    echo file_get_contents(__DIR__ . '/maintenance.html');
