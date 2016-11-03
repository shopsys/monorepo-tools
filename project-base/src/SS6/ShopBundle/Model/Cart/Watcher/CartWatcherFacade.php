<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\FlashMessage\FlashMessageSender;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;

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
	 * @var \SS6\ShopBundle\Component\FlashMessage\FlashMessageSender
	 */
	private $flashMessageSender;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	public function __construct(
		FlashMessageSender $flashMessageSender,
		EntityManager $em,
		CartWatcherService $cartWatcherService,
		CurrentCustomer $currentCustomer
	) {
		$this->flashMessageSender = $flashMessageSender;
		$this->em = $em;
		$this->cartWatcherService = $cartWatcherService;
		$this->currentCustomer = $currentCustomer;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function checkCartModifications(Cart $cart) {
		$this->checkNotListableItems($cart);
		$this->checkModifiedPrices($cart);

		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	private function checkModifiedPrices(Cart $cart) {
		$modifiedItems = $this->cartWatcherService->getModifiedPriceItemsAndUpdatePrices($cart);

		foreach ($modifiedItems as $cartItem) {
			$this->flashMessageSender->addInfoFlashTwig(
				t('Byla změněna cena zboží <strong>{{ name }}</strong>'
				. ', které máte v košíku. Prosím, překontrolujte si objednávku.'),
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
			try {
				$productName = $cartItem->getName();
				$this->flashMessageSender->addErrorFlashTwig(
					t('Zboží <strong>{{ name }}</strong>'
					. ', které jste měli v košíku, již není v nabídce. Prosím, překontrolujte si objednávku.'),
					['name' => $productName]
				);
			} catch (\SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException $e) {
				$this->flashMessageSender->addErrorFlash(
					t('Zboží, které jste měli v košíku, již není v nabídce. Prosím, překontrolujte si objednávku.')
				);
			}

			$cart->removeItemById($cartItem->getId());
			$this->em->remove($cartItem);
		}

		$this->em->flush();
	}

}
