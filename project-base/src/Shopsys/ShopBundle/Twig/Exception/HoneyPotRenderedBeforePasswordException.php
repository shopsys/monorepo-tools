<?php

namespace Shopsys\ShopBundle\Twig\Exception;

use Exception;
use Shopsys\ShopBundle\Form\HoneyPotType;
use Shopsys\ShopBundle\Twig\Exception\TwigException;
use Shopsys\ShopBundle\Twig\HoneyPotExtension;
use Twig_Error;

class HoneyPotRenderedBeforePasswordException extends Twig_Error implements TwigException
{
    /**
     * @param \Exception|null $previous
     */
    public function __construct(Exception $previous = null) {
        $message = sprintf(
            '%s was rendered before password field.'
            . ' Render honeypot after password field to overcome issues when Firefox prefills input'
            . ' before password with saved username.',
            HoneyPotType::class,
            HoneyPotExtension::PASSWORD_FIELD_NAME
        );

        // let the parent exception guess lineno and filename
        parent::__construct($message, -1, null, $previous);
    }
}
