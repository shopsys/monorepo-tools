<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

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
     * @var string
     */
    public $image;

    /**
     * @var int|null
     */
    public $domainId;

    /**
     * @param string|null $name
     * @param string|null $type
     * @param string|null $code
     * @param string|null $link
     * @param string|null $positionName
     * @param bool $hidden
     * @param int|null $domainId
     */
    public function __construct(
        $name = null,
        $type = null,
        $code = null,
        $link = null,
        $positionName = null,
        $hidden = false,
        $domainId = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->code = $code;
        $this->link = $link;
        $this->positionName = $positionName;
        $this->hidden = $hidden;
        $this->domainId = $domainId;
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
