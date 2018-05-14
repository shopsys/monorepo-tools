<?php

namespace Shopsys\FrameworkBundle\Component\FileUpload;

class ImageUploadData
{
    /**
     * @var string[]
     */
    public $uploadedFiles = [];

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public $imagesToDelete = [];

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public $orderedImages = [];
}
