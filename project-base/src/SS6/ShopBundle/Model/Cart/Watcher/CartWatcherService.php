<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
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
		$modifiedItems = array();
		foreach ($cart->getItems() as $cartItem) {
			$productPrice = $this->productPriceCalculationForUser->calculatePriceByCurrentUser($cartItem->getProduct());
			if ($cartItem->getWatchedPrice() != $productPrice->getPriceWithVat()) {
				$modifiedItems[] = $cartItem;
			}
			$cartItem->setWatchedPrice($productPrice->getPriceWithVat());
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
			try {
				$productDomain = $this->productRepository->findProductDomainByProductAndDomainId($item->getProduct(), $this->domain->getId());
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
				$productDomain = null;
			}

			if ($productDomain === null || !$productDomain->isVisible()) {
				$notVisibleItems[] = $item;
			}
		}
		return $notVisibleItems;
	}

}
