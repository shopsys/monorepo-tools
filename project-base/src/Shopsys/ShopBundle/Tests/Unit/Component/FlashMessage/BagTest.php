<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\FlashMessage;

use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;

class BagTest extends FunctionalTestCase {

	public function testAddFrontVsAdmin() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.component.flash_message.bag.admin');
		/* @var $flashMessageAdmin \Shopsys\ShopBundle\Component\FlashMessage\Bag */
		$flashMessageFront = $this->getContainer()->get('ss6.shop.component.flash_message.bag.front');
		/* @var $flashMessageAdmin \Shopsys\ShopBundle\Component\FlashMessage\Bag */

		$errorMessageAdmin = 'Error message admin';
		$errorMessageFront = 'Error message front';
		$successMessageAdmin = 'Success message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);
		$flashMessageAdmin->addSuccess($successMessageAdmin);
		$flashMessageFront->addError($errorMessageFront);

		$this->assertSame([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
		$this->assertSame([], $flashMessageAdmin->getInfoMessages());
		$this->assertSame([$successMessageAdmin], $flashMessageAdmin->getSuccessMessages());
		$this->assertSame([$errorMessageFront], $flashMessageFront->getErrorMessages());
		$this->assertSame([], $flashMessageFront->getInfoMessages());
		$this->assertSame([], $flashMessageFront->getSuccessMessages());
	}

	public function testAddArrayOfMessages() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.component.flash_message.bag.admin');
		/* @var $flashMessageAdmin \Shopsys\ShopBundle\Component\FlashMessage\Bag */

		$errorMessagesAdmin = ['First error message admin', 'Second error message admin'];

		$flashMessageAdmin->addError($errorMessagesAdmin);

		$this->assertSame($errorMessagesAdmin, $flashMessageAdmin->getErrorMessages());
	}

	public function testGetUniqueMessage() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.component.flash_message.bag.admin');
		/* @var $flashMessageAdmin \Shopsys\ShopBundle\Component\FlashMessage\Bag */

		$errorMessageAdmin = 'Error message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);
		$flashMessageAdmin->addError($errorMessageAdmin);

		$this->assertSame([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
	}

	public function testGetAndClearBag() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.component.flash_message.bag.admin');
		/* @var $flashMessageAdmin \Shopsys\ShopBundle\Component\FlashMessage\Bag */

		$errorMessageAdmin = 'Error message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);

		$this->assertSame([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
		$this->assertSame([], $flashMessageAdmin->getErrorMessages());
	}

	public function testIsEmpty() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.component.flash_message.bag.admin');
		/* @var $flashMessageAdmin \Shopsys\ShopBundle\Component\FlashMessage\Bag */

		// clearing after previous tests
		$flashMessageAdmin->getErrorMessages();
		$flashMessageAdmin->getInfoMessages();
		$flashMessageAdmin->getSuccessMessages();

		$this->assertTrue($flashMessageAdmin->isEmpty());
		$flashMessageAdmin->addInfo('Some message');
		$this->assertFalse($flashMessageAdmin->isEmpty());
		$this->assertFalse($flashMessageAdmin->isEmpty(), 'Flash message cannot modified content after call isEmpty()');
	}
}
