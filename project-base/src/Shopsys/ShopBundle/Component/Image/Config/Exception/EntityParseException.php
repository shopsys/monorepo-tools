<?php

namespace Shopsys\ShopBundle\Component\Image\Config\Exception;

use Exception;

class EntityParseException extends Exception implements ImageConfigException
{

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @param string $entityClass
     * @param \Exception|null $previous
     */
    public function __construct($entityClass, Exception $previous = null) {
        $this->entityClass = $entityClass;

        $message = sprintf('Parsing of config entity class "%s" failed.', $this->entityClass);
        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string
     */
    public function getEntityClass() {
        return $this->entityClass;
    }
}
