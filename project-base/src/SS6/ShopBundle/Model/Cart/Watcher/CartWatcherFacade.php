<?php

namespace SS6\ShopBundle\Model\Cart\Watcher;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService;
use SS6\ShopBundle\Model\FlashMessage\Bag;

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
	 * @var \SS6\ShopBundle\Model\FlashMessage\Bag
	 */
	private $flashMessageBag;
	
	/**
	 * @param \SS6\ShopBundle\Model\FlashMessage\Bag $flashMessageBag
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Cart\CartService $cartWatcherService
	 */
	public function __construct(
		Bag $flashMessageBag,
		EntityManager $em,
		CartWatcherService $cartWatcherService
	) {
		$this->flashMessageBag = $flashMessageBag;
		$this->em = $em;
		$this->cartWatcherService = $cartWatcherService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function checkCartModifications(Cart $cart) {
		$this->cartWatcherService->showErrorOnModifiedItems($cart);

		$notVisibleItems = $this->cartWatcherService->getNotVisibleItems($cart);
		foreach ($notVisibleItems as $cartItem) {
			$this->flashMessageBag->addError('Zboží ' . $cartItem->getName() .
				', které jste měl v košíku, již není v nabídce. Prosím, překontrolujte si objednávku.');
			$cart->removeItemById($cartItem->getId());
			$this->em->remove($cartItem);
		}

		$this->em->flush();
	}
}
