<?php

namespace Shopsys\FrameworkBundle\Component\Image\Processing;

use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageAdditionalSizeConfig;
use Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig;
use Symfony\Component\Filesystem\Filesystem;

class ImageProcessor
{
    public const EXTENSION_JPEG = 'jpeg';
    public const EXTENSION_JPG = 'jpg';
    public const EXTENSION_PNG = 'png';
    public const EXTENSION_GIF = 'gif';

    /**
     * @var string[]
     */
    protected $supportedImageExtensions;

    /**
     * @var \Intervention\Image\ImageManager
     */
    protected $imageManager;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $localFilesystem;

    /**
     * @param \Intervention\Image\ImageManager $imageManager
     * @param \League\Flysystem\FilesystemInterface $filesystem
     * @param \Symfony\Component\Filesystem\Filesystem $localFilesystem
     */
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

        $image = $this->createInterventionImage($filepath)->save($newFilepath);
        if (realpath($filepath) !== realpath($newFilepath)) {
            $this->localFilesystem->remove($filepath);
        }

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
     * @param \Intervention\Image\Image $image
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageSizeConfig $sizeConfig
     * @param \Shopsys\FrameworkBundle\Component\Image\Config\ImageAdditionalSizeConfig $additionalSizeConfig
     */
    public function resizeByAdditionalSizeConfig(Image $image, ImageSizeConfig $sizeConfig, ImageAdditionalSizeConfig $additionalSizeConfig)
    {
        $this->resize($image, $additionalSizeConfig->getWidth(), $additionalSizeConfig->getHeight(), $sizeConfig->getCrop());
    }

    /**
     * @return string[]
     */
    public function getSupportedImageExtensions()
    {
        return $this->supportedImageExtensions;
    }
}
