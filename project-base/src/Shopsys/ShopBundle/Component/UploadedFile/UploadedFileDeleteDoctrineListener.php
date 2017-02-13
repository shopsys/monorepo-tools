<?php

namespace Shopsys\ShopBundle\Component\UploadedFile;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFile;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UploadedFileDeleteDoctrineListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig
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
     * @return \Shopsys\ShopBundle\Component\UploadedFile\UploadedFileFacade
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
