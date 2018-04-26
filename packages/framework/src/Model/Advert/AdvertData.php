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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\Advert $advert
     */
    public function setFromEntity(Advert $advert)
    {
        $this->name = $advert->getName();
        $this->type = $advert->getType();
        $this->code = $advert->getCode();
        $this->link = $advert->getLink();
        $this->positionName = $advert->getPositionName();
        $this->hidden = $advert->isHidden();
        $this->domainId = $advert->getDomainId();
    }
}
