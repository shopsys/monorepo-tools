<?php

namespace Shopsys\FrameworkBundle\Model\Advert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="adverts")
 * @ORM\Entity
 */
class Advert
{
    const TYPE_IMAGE = 'image';
    const TYPE_CODE = 'code';

    /**
     * @deprecated Use of position constants is discouraged, use literal strings instead.
     */
    const POSITION_HEADER = 'header';
    const POSITION_FOOTER = 'footer';
    const POSITION_PRODUCT_LIST = 'productList';
    const POSITION_LEFT_SIDEBAR = 'leftSidebar';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $link;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $positionName;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $hidden;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertData $advert
     */
    public function __construct(AdvertData $advert)
    {
        $this->domainId = $advert->domainId;
        $this->name = $advert->name;
        $this->type = $advert->type;
        $this->code = $advert->code;
        $this->link = $advert->link;
        $this->positionName = $advert->positionName;
        $this->hidden = $advert->hidden;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Advert\AdvertData $advert
     */
    public function edit(AdvertData $advert)
    {
        $this->domainId = $advert->domainId;
        $this->name = $advert->name;
        $this->type = $advert->type;
        $this->code = $advert->code;
        $this->link = $advert->link;
        $this->positionName = $advert->positionName;
        $this->hidden = $advert->hidden;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @return string|null
     */
    public function getPositionName()
    {
        return $this->positionName;
    }
}
