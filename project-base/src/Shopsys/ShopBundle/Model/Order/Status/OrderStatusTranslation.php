<?php

namespace Shopsys\ShopBundle\Model\Order\Status;

use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Prezent\Doctrine\Translatable\Entity\AbstractTranslation;

/**
 * @ORM\Table(name="order_status_translations")
 * @ORM\Entity
 */
class OrderStatusTranslation extends AbstractTranslation
{

    /**
     * @Prezent\Translatable(targetEntity="Shopsys\ShopBundle\Model\Order\Status\OrderStatus")
     */
    protected $translatable;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

}
