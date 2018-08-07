<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileConfig;

class UploadedFileDeleteDoctrineListener
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileConfig
     */
    private $uploadedFileConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileFacade
     */
    private $uploadedFileFacade;

    public function __construct(
        UploadedFileConfig $uploadedFileConfig,
        UploadedFileFacade $uploadedFileFacade
    ) {
        $this->uploadedFileConfig = $uploadedFileConfig;
        $this->uploadedFileFacade = $uploadedFileFacade;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->uploadedFileConfig->hasUploadedFileEntityConfig($entity)) {
            $uploadedFile = $this->uploadedFileFacade->getUploadedFileByEntity($entity);
            $args->getEntityManager()->remove($uploadedFile);
        } elseif ($entity instanceof UploadedFile) {
            $this->uploadedFileFacade->deleteFileFromFilesystem($entity);
        }
    }
}
