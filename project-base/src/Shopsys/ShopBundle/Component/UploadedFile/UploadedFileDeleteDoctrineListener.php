<?php

namespace Shopsys\FrameworkBundle\Component\UploadedFile;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UploadedFileDeleteDoctrineListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\Config\UploadedFileConfig
     */
    private $uploadedFileConfig;

    public function __construct(
        ContainerInterface $container,
        UploadedFileConfig $uploadedFileConfig
    ) {
        $this->container = $container;
        $this->uploadedFileConfig = $uploadedFileConfig;
    }

    /**
     * Prevent ServiceCircularReferenceException (DoctrineListener cannot be dependent on the EntityManager)
     *
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileFacade
     */
    private function getUploadedFileFacade()
    {
        return $this->container->get(UploadedFileFacade::class);
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($this->uploadedFileConfig->hasUploadedFileEntityConfig($entity)) {
            $uploadedFile = $this->getUploadedFileFacade()->getUploadedFileByEntity($entity);
            $args->getEntityManager()->remove($uploadedFile);
        } elseif ($entity instanceof UploadedFile) {
            $this->getUploadedFileFacade()->deleteFileFromFilesystem($entity);
        }
    }
}
