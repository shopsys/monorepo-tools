<?php

namespace Shopsys\ShopBundle\Model\Advert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="adverts")
 * @ORM\Entity
 */
class Advert {

    const TYPE_IMAGE = 'image';
    const TYPE_CODE = 'code';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $domainId;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $positionName;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @param \Shopsys\ShopBundle\Model\Advert\AdvertData $advert
     */
    public function __construct(AdvertData $advert) {
        $this->domainId = $advert->domainId;
        $this->name = $advert->name;
        $this->type = $advert->type;
        $this->code = $advert->code;
        $this->link = $advert->link;
        $this->positionName = $advert->positionName;
        $this->hidden = $advert->hidden;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Advert\AdvertData $advert
     */
    public function edit(AdvertData $advert) {
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
    public function getId() {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDomainId() {
        return $this->domainId;
    }

    /**
     * @return string|null
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @return string|null
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isHidden() {
        return $this->hidden;
    }

    /**
     * @return string|null
     */
    public function getPositionName() {
        return $this->positionName;
    }

}
