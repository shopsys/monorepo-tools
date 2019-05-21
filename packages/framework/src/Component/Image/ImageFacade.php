<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Image;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\String\TransformString;

class ImageFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig
     */
    protected $imageConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageRepository
     */
    protected $imageRepository;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var \League\Flysystem\MountManager
     */
    protected $mountManager;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    protected $fileUpload;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageLocator
     */
    protected $imageLocator;

    /**
     * @var string
     */
    protected $imageUrlPrefix;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFactoryInterface
     */
    protected $imageFactory;

    /**
     * @param mixed $imageUrlPrefix
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig $imageConfig
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageRepository $imageRepository
     * @param \League\Flysystem\FilesystemInterface $filesystem
     * @param \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload $fileUpload
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageLocator $imageLocator
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFactoryInterface $imageFactory
     * @param \League\Flysystem\MountManager $mountManager
     * @param \League\Flysystem\MountManager;
     */
    public function __construct(
        $imageUrlPrefix,
        EntityManagerInterface $em,
        ImageConfig $imageConfig,
        ImageRepository $imageRepository,
        FilesystemInterface $filesystem,
        FileUpload $fileUpload,
        ImageLocator $imageLocator,
        ImageFactoryInterface $imageFactory,
        MountManager $mountManager
    ) {
        $this->imageUrlPrefix = $imageUrlPrefix;
        $this->em = $em;
        $this->imageConfig = $imageConfig;
        $this->imageRepository = $imageRepository;
        $this->filesystem = $filesystem;
        $this->fileUpload = $fileUpload;
        $this->imageLocator = $imageLocator;
        $this->imageFactory = $imageFactory;
        $this->mountManager = $mountManager;
    }

    /**
     * @param object $entity
     * @param array $temporaryFilenames
     * @param string|null $type
     */
    public function uploadImage($entity, $temporaryFilenames, $type)
    {
        if (count($temporaryFilenames) > 0) {
            $entitiesForFlush = [];
            $imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
            $entityId = $this->getEntityId($entity);
            $oldImage = $this->imageRepository->findImageByEntity($imageEntityConfig->getEntityName(), $entityId, $type);

            if ($oldImage !== null) {
                $this->em->remove($oldImage);
                $entitiesForFlush[] = $oldImage;
            }

            $newImage = $this->imageFactory->create(
                $imageEntityConfig->getEntityName(),
                $entityId,
                $type,
                array_pop($temporaryFilenames)
            );
            $this->em->persist($newImage);
            $entitiesForFlush[] = $newImage;

            $this->em->flush($entitiesForFlush);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[] $orderedImages
     */
    public function saveImageOrdering($orderedImages)
    {
        $this->setImagePositionsByOrder($orderedImages);
        $this->em->flush($orderedImages);
    }

    /**
     * @param object $entity
     * @param array|null $temporaryFilenames
     * @param string|null $type
     */
    public function uploadImages($entity, $temporaryFilenames, $type)
    {
        if ($temporaryFilenames !== null && count($temporaryFilenames) > 0) {
            $imageEntityConfig = $this->imageConfig->getImageEntityConfig($entity);
            $entityId = $this->getEntityId($entity);

            $images = $this->imageFactory->createMultiple($imageEntityConfig, $entityId, $type, $temporaryFilenames);
            foreach ($images as $image) {
                $this->em->persist($image);
            }
            $this->em->flush();
        }
    }

    /**
     * @param object $entity
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[] $images
     */
    public function deleteImages($entity, array $images)
    {
        $entityName = $this->imageConfig->getEntityName($entity);
        $entityId = $this->getEntityId($entity);

        // files will be deleted in doctrine listener
        foreach ($images as $image) {
            $image->checkForDelete($entityName, $entityId);
        }

        foreach ($images as $image) {
            $this->em->remove($image);
        }
    }

    /**
     * @param object $entity
     * @param string|null $type
     * @return \Shopsys\FrameworkBundle\Component\Image\Image
     */
    public function getImageByEntity($entity, $type)
    {
        return $this->imageRepository->getImageByEntity(
            $this->imageConfig->getEntityName($entity),
            $this->getEntityId($entity),
            $type
        );
    }

    /**
     * @param object $entity
     * @param string|null $type
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public function getImagesByEntityIndexedById($entity, $type)
    {
        return $this->imageRepository->getImagesByEntityIndexedById(
            $this->imageConfig->getEntityName($entity),
            $this->getEntityId($entity),
            $type
        );
    }

    /**
     * @param object $entity
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public function getAllImagesByEntity($entity)
    {
        return $this->imageRepository->getAllImagesByEntity(
            $this->imageConfig->getEntityName($entity),
            $this->getEntityId($entity)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     */
    public function deleteImageFiles(Image $image)
    {
        $entityName = $image->getEntityName();
        $imageConfig = $this->imageConfig->getEntityConfigByEntityName($entityName);
        foreach ($imageConfig->getSizeConfigs() as $sizeConfig) {
            $filepath = $this->imageLocator->getAbsoluteImageFilepath($image, $sizeConfig->getName());

            if ($this->filesystem->has($filepath)) {
                $this->filesystem->delete($filepath);
            }
        }
    }

    /**
     * @param object $entity
     * @return int
     */
    protected function getEntityId($entity)
    {
        $entityMetadata = $this->em->getClassMetadata(get_class($entity));
        $identifier = $entityMetadata->getIdentifierValues($entity);
        if (count($identifier) === 1) {
            return array_pop($identifier);
        }

        $message = 'Entity "' . get_class($entity) . '" has not set primary key or primary key is compound."';
        throw new \Shopsys\FrameworkBundle\Component\Image\Exception\EntityIdentifierException($message);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Image\Config\ImageEntityConfig[]
     */
    public function getAllImageEntityConfigsByClass()
    {
        return $this->imageConfig->getAllImageEntityConfigsByClass();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Component\Image\Image|Object $imageOrEntity
     * @param string|null $sizeName
     * @param string|null $type
     * @return string
     */
    public function getImageUrl(DomainConfig $domainConfig, $imageOrEntity, $sizeName = null, $type = null)
    {
        $image = $this->getImageByObject($imageOrEntity, $type);
        if ($this->imageLocator->imageExists($image)) {
            return $domainConfig->getUrl()
                . $this->imageUrlPrefix
                . $this->imageLocator->getRelativeImageFilepath($image, $sizeName);
        }

        throw new \Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int $id
     * @param string $extension
     * @param string $entityName
     * @param string|null $type
     * @param string|null $sizeName
     * @return string
     */
    public function getImageUrlFromAttributes(
        DomainConfig $domainConfig,
        int $id,
        string $extension,
        string $entityName,
        ?string $type,
        ?string $sizeName = null
    ): string {
        $imageFilepath = $this->imageLocator->getRelativeImageFilepathFromAttributes($id, $extension, $entityName, $type, $sizeName);

        return $domainConfig->getUrl() . $this->imageUrlPrefix . $imageFilepath;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $imageOrEntity
     * @param string|null $sizeName
     * @param string|null $type
     * @return \Shopsys\FrameworkBundle\Component\Image\AdditionalImageData[]
     */
    public function getAdditionalImagesData(DomainConfig $domainConfig, $imageOrEntity, ?string $sizeName, ?string $type)
    {
        $image = $this->getImageByObject($imageOrEntity, $type);

        $entityConfig = $this->imageConfig->getEntityConfigByEntityName($image->getEntityName());
        $sizeConfig = $entityConfig->getSizeConfig($sizeName);

        $result = [];
        foreach ($sizeConfig->getAdditionalSizes() as $additionalSizeIndex => $additionalSizeConfig) {
            $url = $this->getAdditionalImageUrl($domainConfig, $additionalSizeIndex, $image, $sizeName);
            $result[] = new AdditionalImageData($additionalSizeConfig->getMedia(), $url);
        }
        return $result;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int $id
     * @param string $extension
     * @param string $entityName
     * @param string|null $type
     * @param string|null $sizeName
     * @return \Shopsys\FrameworkBundle\Component\Image\AdditionalImageData[]
     */
    public function getAdditionalImagesDataFromAttributes(
        DomainConfig $domainConfig,
        int $id,
        string $extension,
        string $entityName,
        ?string $type,
        ?string $sizeName = null
    ): array {
        $entityConfig = $this->imageConfig->getEntityConfigByEntityName($entityName);
        $sizeConfig = $entityConfig->getSizeConfig($sizeName);

        $result = [];
        foreach ($sizeConfig->getAdditionalSizes() as $additionalSizeIndex => $additionalSizeConfig) {
            $imageFilepath = $this->imageLocator->getRelativeImageFilepathFromAttributes($id, $extension, $entityName, $type, $sizeName, $additionalSizeIndex);
            $url = $domainConfig->getUrl() . $this->imageUrlPrefix . $imageFilepath;

            $result[] = new AdditionalImageData($additionalSizeConfig->getMedia(), $url);
        }

        return $result;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int $additionalSizeIndex
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param string|null $sizeName
     * @return string
     */
    protected function getAdditionalImageUrl(DomainConfig $domainConfig, int $additionalSizeIndex, Image $image, ?string $sizeName)
    {
        if ($this->imageLocator->imageExists($image)) {
            return $domainConfig->getUrl()
                . $this->imageUrlPrefix
                . $this->imageLocator->getRelativeAdditionalImageFilepath($image, $additionalSizeIndex, $sizeName);
        }

        throw new \Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image|Object $imageOrEntity
     * @param string|null $type
     * @return \Shopsys\FrameworkBundle\Component\Image\Image
     */
    public function getImageByObject($imageOrEntity, $type = null)
    {
        if ($imageOrEntity instanceof Image) {
            return $imageOrEntity;
        } else {
            return $this->getImageByEntity($imageOrEntity, $type);
        }
    }

    /**
     * @param int $imageId
     * @return \Shopsys\FrameworkBundle\Component\Image\Image
     */
    public function getById($imageId)
    {
        return $this->imageRepository->getById($imageId);
    }

    /**
     * @param object $sourceEntity
     * @param object $targetEntity
     */
    public function copyImages($sourceEntity, $targetEntity)
    {
        $sourceImages = $this->getAllImagesByEntity($sourceEntity);
        $targetImages = [];
        foreach ($sourceImages as $sourceImage) {
            $this->mountManager->copy(
                'main://' . $this->imageLocator->getAbsoluteImageFilepath($sourceImage, ImageConfig::ORIGINAL_SIZE_NAME),
                'local://' . TransformString::removeDriveLetterFromPath($this->fileUpload->getTemporaryFilepath($sourceImage->getFilename()))
            );

            $targetImage = $this->imageFactory->create(
                $this->imageConfig->getImageEntityConfig($targetEntity)->getEntityName(),
                $this->getEntityId($targetEntity),
                $sourceImage->getType(),
                $sourceImage->getFilename()
            );

            $this->em->persist($targetImage);
            $targetImages[] = $targetImage;
        }
        $this->em->flush($targetImages);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[] $orderedImages
     */
    protected function setImagePositionsByOrder($orderedImages)
    {
        $position = 0;
        foreach ($orderedImages as $image) {
            $image->setPosition($position);
            $position++;
        }
    }

    /**
     * @param int[] $entityIds
     * @param string $entityClass FQCN
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public function getImagesByEntitiesIndexedByEntityId(array $entityIds, string $entityClass): array
    {
        $entityName = $this->imageConfig->getImageEntityConfigByClass($entityClass)->getEntityName();

        return $this->imageRepository->getMainImagesByEntitiesIndexedByEntityId($entityIds, $entityName);
    }
}
