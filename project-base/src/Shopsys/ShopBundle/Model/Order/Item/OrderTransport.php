<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Item\OrderTransportData;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\Transport;

/**
 * @ORM\Entity
 */
class OrderTransport extends OrderItem {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Transport\Transport")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $transport;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param string $name
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param string $vatPercent
	 * @param int $quantity
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function __construct(
		Order $order,
		$name,
		Price $price,
		$vatPercent,
		$quantity,
		Transport $transport
	) {
		parent::__construct(
			$order,
			$name,
			$price,
			$vatPercent,
			$quantity,
			null,
			null
		);
		$this->transport = $transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItemData $orderTransportData
	 */
	public function edit(OrderItemData $orderTransportData) {
		if ($orderTransportData instanceof OrderTransportData) {
			$this->transport = $orderTransportData->transport;
			parent::edit($orderTransportData);
		} else {
			throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidArgumentException(
				'Instance of ' . OrderTransportData::class . ' is required as argument.'
			);
		}
	}

}
