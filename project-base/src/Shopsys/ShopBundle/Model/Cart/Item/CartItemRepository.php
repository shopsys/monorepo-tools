<?php

namespace Shopsys\ShopBundle\Model\Cart\Item;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Cart\Item\CartItem;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;

class CartItemRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getCartItemRepository() {
		return $this->em->getRepository(CartItem::class);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @return \Shopsys\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	public function getAllByCustomerIdentifier(CustomerIdentifier $customerIdentifier) {
		$criteria = [];
		if ($customerIdentifier->getUser() !== null) {
			$criteria['user'] = $customerIdentifier->getUser()->getId();
		} else {
			$criteria['cartIdentifier'] = $customerIdentifier->getCartIdentifier();
		}

		return $this->getCartItemRepository()->findBy($criteria, ['id' => 'desc']);
	}

}
