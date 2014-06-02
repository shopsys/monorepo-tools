<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\FlashMessage\FlashMessage;

class CartWatcherService {

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\FlashMessage
	 */
	private $flashMessage;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\FlashMessage $flashMessage
	 */
	public function __construct(FlashMessage $flashMessage) {
		$this->flashMessage = $flashMessage;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function showErrorOnModifiedItems(Cart $cart) {
		foreach ($this->getModifiedItems($cart) as $item) {
			/* @var $item \SS6\ShopBundle\Model\Cart\CartItem */
			$this->flashMessage->addInfo('Byla změněna cena zboží ' . $item->getName() .
				', které máte v košíku. Prosím, překontrolujte si objednávku.');
			$item->setWatchedPrice($item->getPrice());
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Cart\CartItem[]
	 */
	private function getModifiedItems(Cart $cart) {
		$modifiedItems = array();
		foreach ($cart->getItems() as $item) {
			if ($item->getWatchedPrice() !== $item->getPrice()) {
				$modifiedItems[] = $item;
			}
		}
		return $modifiedItems;
	}

}
