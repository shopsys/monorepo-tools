<?php

namespace Shopsys\FrameworkBundle\Component\Image\Processing;

use Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException;
use Shopsys\FrameworkBundle\Component\Image\Image;
use Shopsys\FrameworkBundle\Component\Image\ImageRepository;

class ImageGeneratorFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageRepository
     */
    protected $imageRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageGenerator
     */
    protected $imageGenerator;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageRepository $imageRepository
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageGenerator $imageGenerator
     */
    public function __construct(
        ImageRepository $imageRepository,
        ImageGenerator $imageGenerator
    ) {
        $this->imageRepository = $imageRepository;
        $this->imageGenerator = $imageGenerator;
    }

    /**
     * @param string $entityName
     * @param int $imageId
     * @param string|null $type
     * @param string|null $sizeName
     * @return string
     */
    public function generateImageAndGetFilepath($entityName, $imageId, $type, $sizeName)
    {
        $image = $this->imageRepository->getById($imageId);

        $this->checkEntityNameAndType($image, $entityName, $type);

        return $this->imageGenerator->generateImageSizeAndGetFilepath($image, $sizeName);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Image $image
     * @param string $entityName
     * @param null|string $type
     */
    protected function checkEntityNameAndType(Image $image, string $entityName, ?string $type): void
    {
        if ($image->getEntityName() !== $entityName) {
            $message = sprintf('Image (ID = %s) does not have entity name "%s"', $image->getId(), $entityName);
            throw new ImageNotFoundException($message);
        }

        if ($image->getType() !== $type) {
            $message = sprintf('Image (ID = %s) does not have type "%s"', $image->getId(), $type);
            throw new ImageNotFoundException($message);
        }
    }

    /**
     * @param string $entityName
     * @param int $imageId
     * @param int $additionalIndex
     * @param string|null $type
     * @param string|null $sizeName
     * @return string
     */
    public function generateAdditionalImageAndGetFilepath(string $entityName, int $imageId, int $additionalIndex, ?string $type, ?string $sizeName): string
    {
        $image = $this->imageRepository->getById($imageId);

        $this->checkEntityNameAndType($image, $entityName, $type);

        return $this->imageGenerator->generateAdditionalImageSizeAndGetFilepath($image, $additionalIndex, $sizeName);
    }
}
