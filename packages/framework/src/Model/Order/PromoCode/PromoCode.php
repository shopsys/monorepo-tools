<?php

namespace Shopsys\FrameworkBundle\Model\Order\PromoCode;

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
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text", unique=true)
     */
    protected $code;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=20, scale=4)
     */
    protected $percent;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     */
    public function __construct(PromoCodeData $promoCodeData)
    {
        $this->code = $promoCodeData->code;
        $this->percent = $promoCodeData->percent;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     */
    public function edit(PromoCodeData $promoCodeData)
    {
        $this->code = $promoCodeData->code;
        $this->percent = $promoCodeData->percent;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }
}
