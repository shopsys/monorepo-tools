<?php

namespace Shopsys\ShopBundle\Controller\Front;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\Processing\ImageGeneratorFacade;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageGeneratorFacade
     */
    private $imageGeneratorFacade;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesystem;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageGeneratorFacade $imageGeneratorFacade
     * @param \League\Flysystem\FilesystemInterface $filesystem
     */
    public function __construct(ImageGeneratorFacade $imageGeneratorFacade, FilesystemInterface $filesystem)
    {
        $this->imageGeneratorFacade = $imageGeneratorFacade;
        $this->filesystem = $filesystem;
    }

    /**
     * @param mixed $entityName
     * @param mixed $type
     * @param mixed $sizeName
     * @param mixed $imageId
     */
    public function getImageAction($entityName, $type, $sizeName, $imageId)
    {
        if ($sizeName === ImageConfig::DEFAULT_SIZE_NAME) {
            $sizeName = null;
        }

        try {
            $imageFilepath = $this->imageGeneratorFacade->generateImageAndGetFilepath($entityName, $imageId, $type, $sizeName);
        } catch (\Shopsys\FrameworkBundle\Component\Image\Exception\ImageException $e) {
            $message = 'Generate image for entity "' . $entityName
                . '" (type=' . $type . ', size=' . $sizeName . ', imageId=' . $imageId . ') failed.';
            throw $this->createNotFoundException($message, $e);
        }

        try {
            $fileStream = $this->filesystem->readStream($imageFilepath);
            $headers = [
                'content-type' => $this->filesystem->getMimetype($imageFilepath),
                'content-size' => $this->filesystem->getSize($imageFilepath),
            ];

            $callback = function () use ($fileStream) {
                $out = fopen('php://output', 'wb');
                stream_copy_to_stream($fileStream, $out);
            };

            return new StreamedResponse($callback, 200, $headers);
        } catch (\Exception $e) {
            $message = 'Response with file "' . $imageFilepath . '" failed.';
            throw $this->createNotFoundException($message, $e);
        }
    }
}
