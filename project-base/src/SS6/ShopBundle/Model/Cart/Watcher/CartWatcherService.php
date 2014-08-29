<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\FlashMessage\Bag;
use SS6\ShopBundle\Model\Product\PriceCalculation;

class CartWatcherService {

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\Bag
	 */
	private $flashMessageBag;

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\Bag $flashMessageBag
	 * @param \SS6\ShopBundle\Model\Product\PriceCalculation $productPriceCalculation
	 */
	public function __construct(
		Bag $flashMessageBag,
		PriceCalculation $productPriceCalculation
	) {
		$this->flashMessageBag = $flashMessageBag;
		$this->productPriceCalculation = $productPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function showErrorOnModifiedItems(Cart $cart) {
		foreach ($this->getModifiedPriceItems($cart) as $cartItem) {
			/* @var $cartItem \SS6\ShopBundle\Model\Cart\Item\CartItem */
			$this->flashMessageBag->addInfo('Byla změněna cena zboží ' . $cartItem->getName() .
				', které máte v košíku. Prosím, překontrolujte si objednávku.');
			$productPrice = $this->productPriceCalculation->calculatePrice($cartItem->getProduct());
			$cartItem->setWatchedPrice($productPrice->getBasePriceWithVat());
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	private function getModifiedPriceItems(Cart $cart) {
		$modifiedItems = array();
		foreach ($cart->getItems() as $cartItem) {
			$productPrice = $this->productPriceCalculation->calculatePrice($cartItem->getProduct());
			if ($cartItem->getWatchedPrice() != $productPrice->getBasePriceWithVat()) {
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
