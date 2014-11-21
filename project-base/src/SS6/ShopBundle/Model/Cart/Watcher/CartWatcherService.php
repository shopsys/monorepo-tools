<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\FlashMessage\FlashMessageSender;
use SS6\ShopBundle\Model\Product\PriceCalculation;
use SS6\ShopBundle\Model\Product\ProductRepository;

class CartWatcherService {

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender
	 */
	private $flashMessageSender;

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender $flashMessageSender
	 * @param \SS6\ShopBundle\Model\Product\PriceCalculation $productPriceCalculation
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 * @param \SS6\ShopBundle\Model\Domain\Domain
	 */
	public function __construct(
		FlashMessageSender $flashMessageSender,
		PriceCalculation $productPriceCalculation,
		ProductRepository $productRepository,
		Domain $domain
	) {
		$this->flashMessageSender = $flashMessageSender;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->productRepository = $productRepository;
		$this->domain = $domain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function showErrorOnModifiedItems(Cart $cart) {
		foreach ($this->getModifiedPriceItems($cart) as $cartItem) {
			/* @var $cartItem \SS6\ShopBundle\Model\Cart\Item\CartItem */
			$this->flashMessageSender->addInfoTwig('Byla změněna cena zboží <strong>{{ name }}</strong>'
				. ', které máte v košíku. Prosím, překontrolujte si objednávku.',
				array('name' => $cartItem->getName()));
			$productPrice = $this->productPriceCalculation->calculatePrice($cartItem->getProduct());
			$cartItem->setWatchedPrice($productPrice->getPriceWithVat());
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
			if ($cartItem->getWatchedPrice() != $productPrice->getPriceWithVat()) {
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
