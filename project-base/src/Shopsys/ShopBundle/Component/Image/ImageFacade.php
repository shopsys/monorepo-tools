<?php

namespace Shopsys\ShopBundle\Component\Image;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\Image;
use Shopsys\ShopBundle\Component\Image\ImageLocator;
use Shopsys\ShopBundle\Component\Image\ImageRepository;
use Shopsys\ShopBundle\Component\Image\ImageService;
use Symfony\Component\Filesystem\Filesystem;

class ImageFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Config\ImageConfig
     */
    private $imageConfig;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageRepository
     */
    private $imageRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageService
     */
    private $imageService;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageLocator
     */
    private $imageLocator;

    /**
     * @var string
     */
    private $imageUrlPrefix;

    public function __construct(
        $imageUrlPrefix,
        EntityManager $em,
        ImageConfig $imageConfig,
        ImageRepository $imageRepository,
        ImageService $imageService,
        Filesystem $filesystem,
        FileUpload $fileUpload,
        ImageLocator $imageLocator
    ) {
        $this->imageUrlPrefix = $imageUrlPrefix;
        $this->em = $em;
        $this->imageConfig = $imageConfig;
        $this->imageRepository = $imageRepository;
        $this->imageService = $imageService;
        $this->filesystem = $filesystem;
        $this->fileUpload = $fileUpload;
        $this->imageLocator = $imageLocator;
    }

    /**
     * @param object $entity
     * @param array|null $temporaryFilenames
     * @param string|null $type
     */
    public function uploadImage($entity, $temporaryFilenames, $type) {
        if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
            $entitiesForFlush = [];
            $imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
            $entityId = $this->getEntityId($entity);
            $oldImage = $this->imageRepository->findImageByEntity($imageEntityConfig->getEntityName(), $entityId, $type);

            if ($oldImage !== null) {
                $this->em->remove($oldImage);
                $entitiesForFlush[] = $oldImage;
            }

            $newImage = $this->imageService->createImage(
                $imageEntityConfig,
                $entityId,
                array_pop($temporaryFilenames),
                $type
            );
            $this->em->persist($newImage);
            $entitiesForFlush[] = $newImage;

            $this->em->flush($entitiesForFlush);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image[] $imagesByPosition
     */
    public function saveImagePositions($imagesByPosition) {
        $this->imageService->setImagePositions($imagesByPosition);
        $this->em->flush($imagesByPosition);
    }

    /**
     * @param object $entity
     * @param array|null $temporaryFilenames
     * @param string|null $type
     */
    public function uploadImages($entity, $temporaryFilenames, $type) {
        if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
            $imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
            $entityId = $this->getEntityId($entity);

            $images = $this->imageService->getUploadedImages($imageEntityConfig, $entityId, $temporaryFilenames, $type);
            foreach ($images as $image) {
                $this->em->persist($image);
            }
            $this->em->flush();
        }
    }

    /**
     * @param object $entity
     * @param \Shopsys\ShopBundle\Component\Image\Image[] $images
     */
    public function deleteImages($entity, array $images) {
        $entityName = $this->imageConfig->getEntityName($entity);
        $entityId = $this->getEntityId($entity);

        // files will be deleted in doctrine listener
        $this->imageService->deleteImages($entityName, $entityId, $images);

        foreach ($images as $image) {
            $this->em->remove($image);
        }
    }

    /**
     * @param object $entity
     * @param string|null $type
     * @return \Shopsys\ShopBundle\Component\Image\Image
     */
    public function getImageByEntity($entity, $type) {
        return $this->imageRepository->getImageByEntity(
            $this->imageConfig->getEntityName($entity),
            $this->getEntityId($entity),
            $type
        );
    }

    /**
     * @param object $entity
     * @param string|null $type
     * @return \Shopsys\ShopBundle\Component\Image\Image[imageId]
     */
    public function getImagesByEntityIndexedById($entity, $type) {
        return $this->imageRepository->getImagesByEntityIndexedById(
            $this->imageConfig->getEntityName($entity),
            $this->getEntityId($entity),
            $type
        );
    }

    /**
     * @param object $entity
     * @return \Shopsys\ShopBundle\Component\Image\Image[]
     */
    public function getAllImagesByEntity($entity) {
        return $this->imageRepository->getAllImagesByEntity(
            $this->imageConfig->getEntityName($entity),
            $this->getEntityId($entity)
        );
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image $image
     */
    public function deleteImageFiles(Image $image) {
        $entityName = $image->getEntityName();
        $imageConfig = $this->imageConfig->getEntityConfigByEntityName($entityName);
        foreach ($imageConfig->getSizeConfigs() as $sizeConfig) {
            $filepath = $this->imageLocator->getAbsoluteImageFilepath($image, $sizeConfig->getName());
            $this->filesystem->remove($filepath);
        }
    }

    /**
     * @param object $entity
     * @return int
     */
    private function getEntityId($entity) {
        $entityMetadata = $this->em->getClassMetadata(get_class($entity));
        $identifier = $entityMetadata->getIdentifierValues($entity);
        if (count($identifier) === 1) {
            return array_pop($identifier);
        }

        $message = 'Entity "' . get_class($entity) . '" has not set primary key or primary key is compound."';
        throw new \Shopsys\ShopBundle\Component\Image\Exception\EntityIdentifierException($message);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Image\Config\ImageEntityConfig[]
     */
    public function getAllImageEntityConfigsByClass() {
        return $this->imageConfig->getAllImageEntityConfigsByClass();
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\ShopBundle\Component\Image\Image|Object $imageOrEntity
     * @param string|null $sizeName
     * @param string|null $type
     * @return string
     */
    public function getImageUrl(DomainConfig $domainConfig, $imageOrEntity, $sizeName = null, $type = null) {
        $image = $this->getImageByObject($imageOrEntity, $type);
        if ($this->imageLocator->imageExists($image)) {
            return $domainConfig->getUrl()
                . $this->imageUrlPrefix
                . $this->imageLocator->getRelativeImageFilepath($image, $sizeName);
        }

        throw new \Shopsys\ShopBundle\Component\Image\Exception\ImageNotFoundException();
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image|Object $imageOrEntity
     * @param string|null $type
     * @return \Shopsys\ShopBundle\Component\Image\Image
     */
    public function getImageByObject($imageOrEntity, $type = null) {
        if ($imageOrEntity instanceof Image) {
            return $imageOrEntity;
        } else {
            return $this->getImageByEntity($imageOrEntity, $type);
        }
    }

    /**
     * @param int $imageId
     * @return \Shopsys\ShopBundle\Component\Image\Image
     */
    public function getById($imageId) {
        return $this->imageRepository->getById($imageId);
    }

    /**
     * @param object $sourceEntity
     * @param object $targetEntity
     */
    public function copyImages($sourceEntity, $targetEntity) {
        $sourceImages = $this->getAllImagesByEntity($sourceEntity);
        $targetImages = [];
        foreach ($sourceImages as $sourceImage) {
            $this->filesystem->copy(
                $this->imageLocator->getAbsoluteImageFilepath($sourceImage, ImageConfig::ORIGINAL_SIZE_NAME),
                $this->fileUpload->getTemporaryFilepath($sourceImage->getFilename()),
                true
            );

            $targetImage = $this->imageService->createImage(
                $this->imageConfig->getImageEntityConfig($targetEntity),
                $this->getEntityId($targetEntity),
                $sourceImage->getFilename(),
                $sourceImage->getType()
            );

            $this->em->persist($targetImage);
            $targetImages[] = $targetImage;
        }
        $this->em->flush($targetImages);
    }
}
