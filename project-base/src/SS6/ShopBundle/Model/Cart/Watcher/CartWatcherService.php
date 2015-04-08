<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\ProductRepository;

class CartWatcherService {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 * @param \SS6\ShopBundle\Model\Domain\Domain
	 */
	public function __construct(
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductRepository $productRepository,
		Domain $domain
	) {
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
		$this->productRepository = $productRepository;
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
	public function getNotVisibleItems(Cart $cart, CurrentCustomer $currentCustomer) {
		$notVisibleItems = [];
		foreach ($cart->getItems() as $item) {
			try {
				$productVisibility = $this->productRepository
					->findProductVisibility(
						$item->getProduct(),
						$currentCustomer->getPricingGroup(),
						$this->domain->getId()
					);
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
				$productVisibility = null;
			}

			if ($productVisibility === null || !$productVisibility->isVisible()) {
				$notVisibleItems[] = $item;
			}
		}
		return $notVisibleItems;
	}

}
