<?php

namespace Shopsys\ShopBundle\Component\Image;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\Image;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Image\ImageLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class ImageDeleteDoctrineListener
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    /**
     * @var \Shopsys\ShopBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageLocator
     */
    private $imageLocator;

    public function __construct(
        ContainerInterface $container,
        Filesystem $filesystem,
        ImageConfig $imageConfig,
        FileUpload $fileUpload,
        ImageLocator $imageLocator
    ) {
        $this->container = $container;
        $this->filesystem = $filesystem;
        $this->imageConfig = $imageConfig;
        $this->fileUpload = $fileUpload;
        $this->imageLocator = $imageLocator;
    }

    /**
     * Prevent ServiceCircularReferenceException (DoctrineListener cannot be dependent on the EntityManager)
     *
     * @return \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private function getImageFacade() {
        return $this->container->get(ImageFacade::class);
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if ($this->imageConfig->hasImageConfig($entity)) {
            $this->deleteEntityImages($entity, $args->getEntityManager());
        } elseif ($entity instanceof Image) {
            $this->getImageFacade()->deleteImageFiles($entity);
        }
    }

    /**
     * @param object $entity
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Component\Image\ImageFacade $imageFacade
     */
    private function deleteEntityImages($entity, EntityManager $em) {
        $images = $this->getImageFacade()->getAllImagesByEntity($entity);
        if (count($images) > 0) {
            foreach ($images as $entity) {
                $em->remove($entity);
            }
        }
    }
}
