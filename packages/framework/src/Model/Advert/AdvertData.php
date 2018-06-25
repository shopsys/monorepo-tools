<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;

class AdvertData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $type;

    /**
     * @var string|null
     */
    public $code;

    /**
     * @var string|null
     */
    public $link;

    /**
     * @var string|null
     */
    public $positionName;

    /**
     * @var bool
     */
    public $hidden;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
     */
    public $image;

    /**
     * @var int|null
     */
    public $domainId;

    public function __construct()
    {
        $this->hidden = false;
        $this->image = new ImageUploadData();
    }
}
