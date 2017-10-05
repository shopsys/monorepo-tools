<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use Exception;

class HeurekaCategoryDownloadFailedException extends Exception
{
    /**
     * @param \Exception $causedBy
     */
    public function __construct(Exception $causedBy)
    {
        $message = sprintf('Download of Heureka categories failed: "%s"', $causedBy->getMessage());

        parent::__construct($message, $causedBy->getCode(), $causedBy);
    }
}
