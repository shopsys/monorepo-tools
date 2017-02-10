<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Image\Config\ImageConfig;
use Shopsys\ShopBundle\Component\Image\Processing\ImageGeneratorFacade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\Image\Processing\ImageGeneratorFacade
     */
    private $imageGeneratorFacade;

    public function __construct(ImageGeneratorFacade $imageGeneratorFacade)
    {
        $this->imageGeneratorFacade = $imageGeneratorFacade;
    }

    public function getImageAction($entityName, $type, $sizeName, $imageId)
    {
        if ($sizeName === ImageConfig::DEFAULT_SIZE_NAME) {
            $sizeName = null;
        }

        try {
            $imageFilepath = $this->imageGeneratorFacade->generateImageAndGetFilepath($entityName, $imageId, $type, $sizeName);
        } catch (\Shopsys\ShopBundle\Component\Image\Exception\ImageException $e) {
            $message = 'Generate image for entity "' . $entityName
                . '" (type=' . $type . ', size=' . $sizeName . ', imageId=' . $imageId . ') failed.';
            throw $this->createNotFoundException($message, $e);
        }

        try {
            return new BinaryFileResponse($imageFilepath);
        } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
            $message = 'Response with file "' . $imageFilepath . '" failed.';
            throw $this->createNotFoundException($message, $e);
        }
    }
}
