<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\PriceCalculation;
use SS6\ShopBundle\Model\FlashMessage\Bag;

class CartWatcherService {

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\Bag
	 */
	private $flashMessageBag;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\PriceCalculation
	 */
	private $cartItemPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\Bag $flashMessageBag
	 * @param \SS6\ShopBundle\Model\Cart\Item\PriceCalculation $cartItemPriceCalculation
	 */
	public function __construct(
		Bag $flashMessageBag,
		PriceCalculation $cartItemPriceCalculation
	) {
		$this->flashMessageBag = $flashMessageBag;
		$this->cartItemPriceCalculation = $cartItemPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function showErrorOnModifiedItems(Cart $cart) {
		foreach ($this->getModifiedPriceItems($cart) as $cartItem) {
			/* @var $cartItem \SS6\ShopBundle\Model\Cart\Item\CartItem */
			$this->flashMessageBag->addInfo('Byla změněna cena zboží ' . $cartItem->getName() .
				', které máte v košíku. Prosím, překontrolujte si objednávku.');
			$cartItemPrice = $this->cartItemPriceCalculation->calculatePrice($cartItem);
			$cartItem->setWatchedPrice($cartItemPrice->getUnitPriceWithVat());
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	private function getModifiedPriceItems(Cart $cart) {
		$modifiedItems = array();
		foreach ($cart->getItems() as $cartItem) {
			$cartItemPrice = $this->cartItemPriceCalculation->calculatePrice($cartItem);
			if ($cartItem->getWatchedPrice() != $cartItemPrice->getUnitPriceWithVat()) {
				$modifiedItems[] = $cartItem;
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
