<?php

namespace Shopsys\FrameworkBundle\Component\Image;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageDeleteDoctrineListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    public function __construct(
        ContainerInterface $container,
        ImageConfig $imageConfig
    ) {
        $this->container = $container;
        $this->imageConfig = $imageConfig;
    }

    /**
     * Prevent ServiceCircularReferenceException (DoctrineListener cannot be dependent on the EntityManager)
     *
     * @return \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private function getImageFacade()
    {
        return $this->container->get(ImageFacade::class);
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
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
     */
    private function deleteEntityImages($entity, EntityManager $em)
    {
        $images = $this->getImageFacade()->getAllImagesByEntity($entity);
        foreach ($images as $image) {
            $em->remove($image);
        }
    }
}
