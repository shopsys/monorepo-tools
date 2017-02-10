<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="promo_codes")
 * @ORM\Entity
 */
class PromoCode
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text", unique=true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=20, scale=4)
     */
    private $percent;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     */
    public function __construct(PromoCodeData $promoCodeData) {
        $this->code = $promoCodeData->code;
        $this->percent = $promoCodeData->percent;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     */
    public function edit(PromoCodeData $promoCodeData) {
        $this->code = $promoCodeData->code;
        $this->percent = $promoCodeData->percent;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getPercent() {
        return $this->percent;
    }
}
