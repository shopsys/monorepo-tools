<?php

declare(strict_types=1);

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
     * @var string
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
        $this->percent = (string)$promoCodeData->percent;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     */
    public function edit(PromoCodeData $promoCodeData): void
    {
        $this->code = $promoCodeData->code;
        $this->percent = (string)$promoCodeData->percent;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getPercent(): string
    {
        return $this->percent;
    }
}
