<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\FlashMessage\Bag;

class CartWatcherService {

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\Bag
	 */
	private $flashMessageBag;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\Bag $flashMessageBag
	 */
	public function __construct(Bag $flashMessageBag) {
		$this->flashMessageBag = $flashMessageBag;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function showErrorOnModifiedItems(Cart $cart) {
		foreach ($this->getModifiedPriceItems($cart) as $item) {
			/* @var $item \SS6\ShopBundle\Model\Cart\Item\CartItem */
			$this->flashMessageBag->addInfo('Byla změněna cena zboží ' . $item->getName() .
				', které máte v košíku. Prosím, překontrolujte si objednávku.');
			$item->setWatchedPrice($item->getPrice());
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	private function getModifiedPriceItems(Cart $cart) {
		$modifiedItems = array();
		foreach ($cart->getItems() as $item) {
			if ($item->getWatchedPrice() !== $item->getPrice()) {
				$modifiedItems[] = $item;
			}
		}
		return $modifiedItems;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	public function getNotVisibleItems(Cart $cart) {
		$notVisibleItems = array();
		foreach ($cart->getItems() as $item) {
			if (!$item->getProduct()->isVisible()) {
				$notVisibleItems[] = $item;
			}
		}
		return $notVisibleItems;
	}

}
