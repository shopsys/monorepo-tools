<?php

namespace Shopsys\ShopBundle\Component\UploadedFile\Config;

class UploadedFileEntityConfig
{

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @param string $entityName
     * @param string $entityClass
     */
    public function __construct($entityName, $entityClass) {
        $this->entityName = $entityName;
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityName() {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getEntityClass() {
        return $this->entityClass;
    }
}
