<?php

namespace Shopsys\ShopBundle\Controller\Front;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageConfig;
use Shopsys\FrameworkBundle\Component\Image\Exception\ImageException;
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
        } catch (ImageException $e) {
            $message = sprintf(
                'Generate image for entity "%s" (type=%s, size=%s, imageId=%s) failed',
                $entityName,
                $type,
                $sizeName,
                $imageId
            );
            throw $this->createNotFoundException($message, $e);
        }

        return $this->sendImage($imageFilepath);
    }

    /**
     * @param mixed $entityName
     * @param mixed $type
     * @param mixed $sizeName
     * @param int $imageId
     * @param int $additionalIndex
     */
    public function getAdditionalImageAction($entityName, $type, $sizeName, int $imageId, int $additionalIndex)
    {
        if ($sizeName === ImageConfig::DEFAULT_SIZE_NAME) {
            $sizeName = null;
        }

        try {
            $imageFilepath = $this->imageGeneratorFacade->generateAdditionalImageAndGetFilepath($entityName, $imageId, $additionalIndex, $type, $sizeName);
        } catch (ImageException $e) {
            $message = sprintf(
                'Generate image for entity "%s" (type=%s, size=%s, imageId=%s, additionalIndex=%s) failed',
                $entityName,
                $type,
                $sizeName,
                $imageId,
                $additionalIndex
            );
            throw $this->createNotFoundException($message, $e);
        }

        return $this->sendImage($imageFilepath);
    }

    /**
     * @param string $imageFilepath
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    protected function sendImage(string $imageFilepath): StreamedResponse
    {
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
