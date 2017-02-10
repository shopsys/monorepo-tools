<?php

namespace Shopsys\ShopBundle\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

class DownloadFileResponse extends Response {

    public function __construct($filename, $fileContent) {
        parent::__construct($fileContent);

        $this->headers->set('Content-type', 'text/html');
        $this->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
    }

}
