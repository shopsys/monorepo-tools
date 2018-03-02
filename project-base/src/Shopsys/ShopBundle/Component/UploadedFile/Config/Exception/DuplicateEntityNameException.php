<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile\Config\Exception;

use Exception;

class DuplicateEntityNameException extends Exception implements UploadedFileConfigException
{
    /**
     * @var string
     */
    private $entityName;

    /**
     * @param string $entityName
     * @param \Exception|null $previous
     */
    public function __construct($entityName, Exception $previous = null)
    {
        $this->entityName = $entityName;

        $message = sprintf('UploadedFile entity name "%s" is not unique.', $this->entityName);
        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }
}
