<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\FlashMessage\FlashMessageSender;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;

class CartWatcherFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService
	 */
	private $cartWatcherService;

	/**
	 * @var \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender
	 */
	private $flashMessageSender;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
	 */
	private $promoCodeFacade;

	public function __construct(
		FlashMessageSender $flashMessageSender,
		EntityManager $em,
		CartWatcherService $cartWatcherService,
		CurrentCustomer $currentCustomer,
		PromoCodeFacade $promoCodeFacade
	) {
		$this->flashMessageSender = $flashMessageSender;
		$this->em = $em;
		$this->cartWatcherService = $cartWatcherService;
		$this->currentCustomer = $currentCustomer;
		$this->promoCodeFacade = $promoCodeFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function checkCartModifications(Cart $cart) {
		$this->checkModifiedPrices($cart);
		$this->checkNotListableItems($cart);
		$this->checkEmptyCart($cart);

		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	private function checkModifiedPrices(Cart $cart) {
		$modifiedItems = $this->cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);

		foreach ($modifiedItems as $cartItem) {
			$this->flashMessageSender->addInfoFlashTwig(
				'Byla změněna cena zboží <strong>{{ name }}</strong>'
				. ', které máte v košíku. Prosím, překontrolujte si objednávku.',
				['name' => $cartItem->getName()]
			);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	private function checkNotListableItems(Cart $cart) {
		$notVisibleItems = $this->cartWatcherService->getNotListableItems($cart, $this->currentCustomer);

		foreach ($notVisibleItems as $cartItem) {
			$this->flashMessageSender->addErrorFlashTwig(
				'Zboží <strong>{{ name }}</strong>'
				. ', které jste měli v košíku, již není v nabídce. Prosím, překontrolujte si objednávku.',
				['name' => $cartItem->getName()]
			);
			$cart->removeItemById($cartItem->getId());
			$this->em->remove($cartItem);
		}

		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	private function checkEmptyCart(Cart $cart) {
		if ($cart->isEmpty()) {
			$this->promoCodeFacade->removeEnteredPromoCode();
		}
	}

}
