<?php

namespace Shopsys\FrameworkBundle\Component\Image\Processing;

use Shopsys\FrameworkBundle\Component\Image\ImageRepository;

class ImageGeneratorFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageRepository
     */
    private $imageRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageGeneratorService
     */
    private $imageGeneratorService;

    public function __construct(
        ImageRepository $imageRepository,
        ImageGeneratorService $imageGeneratorService
    ) {
        $this->imageRepository = $imageRepository;
        $this->imageGeneratorService = $imageGeneratorService;
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

        if ($image->getEntityName() !== $entityName) {
            $message = 'Image (ID = ' . $imageId . ') does not have entity name "' . $entityName . '"';
            throw new \Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException($message);
        }

        if ($image->getType() !== $type) {
            $message = 'Image (ID = ' . $imageId . ') does not have type "' . $type . '"';
            throw new \Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException($message);
        }

        return $this->imageGeneratorService->generateImageSizeAndGetFilepath($image, $sizeName);
    }
}
