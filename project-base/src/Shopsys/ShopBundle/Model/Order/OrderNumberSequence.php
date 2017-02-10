<?php

namespace Shopsys\ShopBundle\Model\Order;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="order_number_sequences")
 * @ORM\Entity
 */
class OrderNumberSequence
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=10, scale=0, nullable=false)
     */
    private $number;

    /**
     * @param int $id
     * @param string $number
     */
    public function __construct($id, $number = 0) {
        $this->id = $id;
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number) {
        $this->number = $number;
    }

}
