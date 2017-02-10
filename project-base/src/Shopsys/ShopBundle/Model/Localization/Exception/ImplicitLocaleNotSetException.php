<?php

namespace Shopsys\ShopBundle\Model\Localization\Exception;

use Exception;
use Prezent\Doctrine\TranslatableBundle\EventListener\LocaleListener;
use Shopsys\ShopBundle\Model\Localization\TranslatableListener;

class ImplicitLocaleNotSetException extends Exception implements LocalizationException
{

    /**
     * @param object $entity
     * @param mixed $entityId
     * @param \Exception|null $previous
     */
    public function __construct($entity, $entityId, Exception $previous = null) {
        $message = sprintf(
            'You tried to get a translation of entity %s (ID: "%s") without specifying a locale'
            . ' and the entity has empty currentLocale. Either specify locale explicitly as an argument'
            . ' of translation() method or check why implicit currentLocale is not set by %s.'
            . ' Maybe the entity was hydrated before currentLocale was set in %s.',
            get_class($entity),
            $entityId,
            LocaleListener::class,
            TranslatableListener::class
        );
        parent::__construct($message, 0, $previous);
    }
}
