<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;

class CartWatcherService {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityRepository
	 */
	private $productVisibilityRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
	 * @param \SS6\ShopBundle\Model\Domain\Domain
	 */
	public function __construct(
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductVisibilityRepository $productVisibilityRepository,
		Domain $domain
	) {
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->productVisibilityRepository = $productVisibilityRepository;
		$this->domain = $domain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	public function getModifiedPriceItemsAndUpdatePrices(Cart $cart) {
		$modifiedItems = [];
		foreach ($cart->getItems() as $cartItem) {
			$productPrice = $this->productPriceCalculationForUser->calculatePriceForCurrentUser($cartItem->getProduct());
			if ($cartItem->getWatchedPrice() != $productPrice->getPriceWithVat()) {
				$modifiedItems[] = $cartItem;
			}
			$cartItem->setWatchedPrice($productPrice->getPriceWithVat());
		}
		return $modifiedItems;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param \SS6\ShopBundle\Model\Customer\CurrentCustomer $currentCustomer
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	public function getNotListableItems(Cart $cart, CurrentCustomer $currentCustomer) {
		$notListableItems = [];
		foreach ($cart->getItems() as $item) {
			try {
				$productVisibility = $this->productVisibilityRepository
					->getProductVisibility(
						$item->getProduct(),
						$currentCustomer->getPricingGroup(),
						$this->domain->getId()
					);
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
				$productVisibility = null;
			}

			try {
				$product = $item->getProduct();
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
				$product = null;
			}

			if ($productVisibility === null || !$productVisibility->isVisible()
				|| $product === null || !$item->getProduct()->isSellable()) {
				$notListableItems[] = $item;
			}
		}

		return $notListableItems;
	}

}
