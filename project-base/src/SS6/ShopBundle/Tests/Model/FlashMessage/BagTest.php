<?php

namespace SS6\ShopBundle\Tests\Model\FlashMessage;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;

class BagTest extends FunctionalTestCase {

	public function testAddFrontVsAdmin() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\Bag */
		$flashMessageFront = $this->getContainer()->get('ss6.shop.flash_message.bag.front');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\Bag */

		$errorMessageAdmin = 'Error message admin';
		$errorMessageFront = 'Error message front';
		$successMessageAdmin = 'Success message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);
		$flashMessageAdmin->addSuccess($successMessageAdmin);
		$flashMessageFront->addError($errorMessageFront);

		$this->assertEquals([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
		$this->assertEquals([], $flashMessageAdmin->getInfoMessages());
		$this->assertEquals([$successMessageAdmin], $flashMessageAdmin->getSuccessMessages());
		$this->assertEquals([$errorMessageFront], $flashMessageFront->getErrorMessages());
		$this->assertEquals([], $flashMessageFront->getInfoMessages());
		$this->assertEquals([], $flashMessageFront->getSuccessMessages());
	}

	public function testAddArrayOfMessages() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\Bag */

		$errorMessagesAdmin = ['First error message admin', 'Second error message admin'];

		$flashMessageAdmin->addError($errorMessagesAdmin);

		$this->assertEquals($errorMessagesAdmin, $flashMessageAdmin->getErrorMessages());
	}

	public function testGetUniqueMessage() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\Bag */

		$errorMessageAdmin = 'Error message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);
		$flashMessageAdmin->addError($errorMessageAdmin);

		$this->assertEquals([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
	}

	public function testGetAndClearBag() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\Bag */

		$errorMessageAdmin = 'Error message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);

		$this->assertEquals([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
		$this->assertEquals([], $flashMessageAdmin->getErrorMessages());
	}

	public function testIsEmpty() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.bag.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\Bag */

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
