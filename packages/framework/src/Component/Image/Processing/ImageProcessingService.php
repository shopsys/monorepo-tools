<?php

namespace Shopsys\FrameworkBundle\Component\Image\Processing;

use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig;
use Symfony\Component\Filesystem\Filesystem;

class ImageProcessingService
{
    const EXTENSION_JPEG = 'jpeg';
    const EXTENSION_JPG = 'jpg';
    const EXTENSION_PNG = 'png';
    const EXTENSION_GIF = 'gif';

    /**
     * @var string[]
     */
    private $supportedImageExtensions;

    /**
     * @var \Intervention\Image\ImageManager
     */
    private $imageManager;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesystem;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $localFilesystem;

    public function __construct(
        ImageManager $imageManager,
        FilesystemInterface $filesystem,
        Filesystem $localFilesystem
    ) {
        $this->imageManager = $imageManager;
        $this->filesystem = $filesystem;

        $this->supportedImageExtensions = [
            self::EXTENSION_JPEG,
            self::EXTENSION_JPG,
            self::EXTENSION_GIF,
            self::EXTENSION_PNG,
        ];
        $this->localFilesystem = $localFilesystem;
    }

    /**
     * @param string $filepath
     * @return \Intervention\Image\Image
     */
    public function createInterventionImage($filepath)
    {
        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

        if (!in_array($extension, $this->supportedImageExtensions, true)) {
            throw new \Shopsys\FrameworkBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException($filepath);
        }
        try {
            if ($this->filesystem->has($filepath)) {
                $file = $this->filesystem->read($filepath);

                return $this->imageManager->make($file);
            } elseif ($this->localFilesystem->exists($filepath)) {
                return $this->imageManager->make($filepath);
            } else {
                throw new \Shopsys\FrameworkBundle\Component\Image\Exception\ImageNotFoundException('File ' . $filepath . ' not found.');
            }
        } catch (\Intervention\Image\Exception\NotReadableException $ex) {
            throw new \Shopsys\FrameworkBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException($filepath, $ex);
        }
    }

    /**
     * @param string $filepath
     * @return string
     */
    public function convertToShopFormatAndGetNewFilename($filepath)
    {
        $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        $newFilepath = pathinfo($filepath, PATHINFO_DIRNAME) . '/' . pathinfo($filepath, PATHINFO_FILENAME) . '.';

        if ($extension === self::EXTENSION_PNG) {
            $newFilepath .= self::EXTENSION_PNG;
        } elseif (in_array($extension, $this->supportedImageExtensions, true)) {
            $newFilepath .= self::EXTENSION_JPG;
        } else {
            throw new \Shopsys\FrameworkBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException($filepath);
        }

        $image = $this->createInterventionImage($filepath);

        $image->encode();
        $this->filesystem->put($newFilepath, $image);

        $this->removeFileIfRenamed($filepath, $newFilepath);

        return $image->filename . '.' . $image->extension;
    }

    /**
     * @param \Intervention\Image\Image $image
     * @param int|null $width
     * @param int|null $height
     * @param bool $crop
     * @return \Intervention\Image\Image
     */
    public function resize(Image $image, $width, $height, $crop = false)
    {
        if ($crop) {
            $image->fit($width, $height, function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        } else {
            $image->resize($width, $height, function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return $image;
    }

    /**
     * @param \Intervention\Image\Image $image
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig $sizeConfig
     */
    public function resizeBySizeConfig(Image $image, ImageSizeConfig $sizeConfig)
    {
        $this->resize($image, $sizeConfig->getWidth(), $sizeConfig->getHeight(), $sizeConfig->getCrop());
    }

    /**
     * @return string[]
     */
    public function getSupportedImageExtensions()
    {
        return $this->supportedImageExtensions;
    }

    /**
     * @param string $filepath
     * @param string $newFilepath
     */
    private function removeFileIfRenamed($filepath, $newFilepath)
    {
        if ($this->filesystem->has($filepath) && $filepath !== $newFilepath) {
            $this->filesystem->delete($filepath);
        } elseif ($this->localFilesystem->exists($filepath) && realpath($filepath) !== realpath($newFilepath)) {
            $this->localFilesystem->remove($filepath);
        }
    }
}
