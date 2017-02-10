<?php

namespace Shopsys\ShopBundle\Component\Image\Config\Exception;

use Exception;

class DuplicateTypeNameException extends Exception implements ImageConfigException
{

    /**
     * @var string|null
     */
    private $typeName;

    /**
     * @param string|null $typeName
     * @param \Exception|null $previous
     */
    public function __construct($typeName = null, Exception $previous = null) {
        $this->typeName = $typeName;

        if ($this->typeName === null) {
            $message = 'Image type NULL is not unique.';
        } else {
            $message = sprintf('Image type "%s" is not unique.', $this->typeName);
        }
        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string|null
     */
    public function getTypeName() {
        return $this->typeName;
    }
}
