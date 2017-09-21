<?php

namespace Shopsys\ShopBundle\Model\Feed\Exception;

use Exception;

class TemplateBlockNotFoundException extends Exception implements FeedException
{
    /**
     * @param string $blockName
     * @param string $templateName
     * @param \Exception|null $previous
     */
    public function __construct($blockName, $templateName, Exception $previous = null)
    {
        $message = sprintf('Block "%s" does not exist in template "%s".', $blockName, $templateName);
        parent::__construct($message, 0, $previous);
    }
}
