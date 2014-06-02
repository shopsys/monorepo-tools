<?php

namespace SS6\ShopBundle\Tests\Model\Cart\Watcher;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\Product;

class CartWatcherServiceTest extends FunctionalTestCase {

	
	public function testShowErrorOnModifiedItems() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$product = new Product('Product 1', null, null, null, null, 100);

		$cartItem = new CartItem($customerIdentifier, $product, 1);
		$cartItems = array($cartItem);
		$cart = new Cart($cartItems);

		$flashMessageFront = $this->getContainer()->get('ss6.shop.flash_message.front');
		/* @var $flashMessageFront \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		
		// clear..
		$flashMessageFront->getErrorMessages();
		$flashMessageFront->getInfoMessages();
		$flashMessageFront->getSuccessMessages();

		$cartWatcherService = $this->getContainer()->get('ss6.shop.cart.cart_watcher_service');
		/* @var $cartWatcherService \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherService */
		
		$cartWatcherService->showErrorOnModifiedItems($cart);
		$this->assertTrue($flashMessageFront->isEmpty());

		$product->edit('Product 1', null, null, null, null, 200, null, null, null, null);
		$cartWatcherService->showErrorOnModifiedItems($cart);
		$this->assertFalse($flashMessageFront->isEmpty());
	}

}
