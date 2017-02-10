<?php

namespace Shopsys\ShopBundle\Component\Filesystem\Exception;

use Exception;

class DirectoryDoesNotExistException extends Exception implements FilesystemException
{
    /**
     * @param string $path
     * @param \Exception|null $previous
     */
    public function __construct($path, Exception $previous = null) {
        $message = sprintf('Path "%s" must exist.', $path);

        parent::__construct($message, 0, $previous);
    }
}
