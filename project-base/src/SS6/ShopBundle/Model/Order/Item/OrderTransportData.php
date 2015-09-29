<?php

namespace SS6\ShopBundle\Model\Order\Item;

use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;

class OrderTransportData extends OrderItemData {

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	public $transport;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $orderTransport
	 */
	public function setFromEntity(OrderItem $orderTransport) {
		if ($orderTransport instanceof OrderTransport) {
			$this->transport = $orderTransport->getTransport();
			parent::setFromEntity($orderTransport);
		} else {
			throw new \SS6\ShopBundle\Model\Order\Item\Exception\InvalidArgumentException(
				'Instance of ' . OrderTransport::class . ' is required as argument.'
			);
		}
	}

}
