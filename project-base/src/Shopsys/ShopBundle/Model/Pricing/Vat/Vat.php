<?php

namespace Shopsys\ShopBundle\Model\Pricing\Vat;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;

/**
 * @ORM\Table(name="vats")
 * @ORM\Entity
 */
class Vat
{
    const SETTING_DEFAULT_VAT = 'defaultVatId';

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
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=4)
     */
    private $percent;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\Vat|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Pricing\Vat\Vat")
     * @ORM\JoinColumn(nullable=true)
     */
    private $replaceWith;

    public function __construct(VatData $vatData)
    {
        $this->name = $vatData->name;
        $this->percent = $vatData->percent;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Vat\Vat|null
     */
    public function getReplaceWith()
    {
        return $this->replaceWith;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\VatData $vatData
     */
    public function edit(VatData $vatData)
    {
        $this->name = $vatData->name;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Vat\Vat $newVat
     */
    public function markForDeletion(Vat $newVat)
    {
        $this->replaceWith = $newVat;
    }

    public function isMarkedAsDeleted()
    {
        return $this->replaceWith !== null;
    }
}
