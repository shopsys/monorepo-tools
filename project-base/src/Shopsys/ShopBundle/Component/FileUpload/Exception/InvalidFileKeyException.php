<?php

namespace Shopsys\ShopBundle\Component\FileUpload\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Debug;

class InvalidFileKeyException extends Exception implements FileUploadException
{
    /**
     * @param mixed $key
     * @param \Exception|null $previous
     */
    public function __construct($key, Exception $previous = null) {
        parent::__construct('Upload file key ' . Debug::export($key) . ' is invalid', 0, $previous);
    }
}
