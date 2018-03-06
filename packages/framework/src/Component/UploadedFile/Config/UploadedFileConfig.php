<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile\Config;

class UploadedFileConfig
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileEntityConfig[]
     */
    private $uploadedFileEntityConfigsByClass;

    /**
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileEntityConfig[] $uploadedFileEntityConfigsByClass
     */
    public function __construct(array $uploadedFileEntityConfigsByClass)
    {
        $this->uploadedFileEntityConfigsByClass = $uploadedFileEntityConfigsByClass;
    }

    /**
     * @param Object $entity
     * @return string
     */
    public function getEntityName($entity)
    {
        return $this->getUploadedFileEntityConfig($entity)->getEntityName();
    }

    /**
     * @param Object $entity
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileEntityConfig
     */
    public function getUploadedFileEntityConfig($entity)
    {
        foreach ($this->uploadedFileEntityConfigsByClass as $className => $entityConfig) {
            if ($entity instanceof $className) {
                return $entityConfig;
            }
        }

        throw new \Shopsys\FrameworkBundle\Component\UploadedFile\Config\Exception\UploadedFileEntityConfigNotFoundException(
            $entity ? get_class($entity) : null
        );
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function hasUploadedFileEntityConfig($entity)
    {
        foreach ($this->uploadedFileEntityConfigsByClass as $className => $entityConfig) {
            if ($entity instanceof $className) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileEntityConfig[]
     */
    public function getAllUploadedFileEntityConfigs()
    {
        return $this->uploadedFileEntityConfigsByClass;
    }
}
