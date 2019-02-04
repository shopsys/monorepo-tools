<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile\Config;

class UploadedFileEntityConfig
{
    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @param string $entityName
     * @param string $entityClass
     */
    public function __construct($entityName, $entityClass)
    {
        $this->entityName = $entityName;
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }
}
