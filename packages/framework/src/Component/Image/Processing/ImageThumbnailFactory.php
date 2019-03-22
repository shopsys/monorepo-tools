<?php

namespace Shopsys\FrameworkBundle\Component\Image\Processing;

class ImageThumbnailFactory
{
    /** @access protected */
    const THUMBNAIL_WIDTH = 140;
    /** @access protected */
    const THUMBNAIL_HEIGHT = 200;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\Processing\ImageProcessor $imageProcessor
     */
    public function __construct(ImageProcessor $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @param string $filepath
     * @return \Intervention\Image\Image
     */
    public function getImageThumbnail($filepath)
    {
        $image = $this->imageProcessor->createInterventionImage($filepath);
        $this->imageProcessor->resize($image, static::THUMBNAIL_WIDTH, static::THUMBNAIL_HEIGHT);

        return $image;
    }
}
