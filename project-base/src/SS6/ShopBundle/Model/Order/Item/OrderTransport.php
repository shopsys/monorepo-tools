<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Order\Item\OrderItemAbstract;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Entity
 */
class OrderTransport extends OrderItemAbstract {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Transport\Transport")
	 */
	private $transport;
	
	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param string $name
	 * @param string $price
	 * @param int $quantity
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function __construct(Order $order, $name, $price, $quantity, Transport $transport) {
		parent::__construct($order, $name, $price, $quantity);
		$this->transport = $transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

}
