<?php

namespace SS6\ShopBundle\TestsDb\Model\FlashMessage;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;

class PaymentTest extends FunctionalTestCase {
	
	public function testAddFrontVsAdmin() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		$flashMessageFront = $this->getContainer()->get('ss6.shop.flash_message.front');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$errorMessageAdmin = 'Error message admin';
		$errorMessageFront = 'Error message front';
		$successMessageAdmin = 'Success message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);
		$flashMessageAdmin->addSuccess($successMessageAdmin);
		$flashMessageFront->addError($errorMessageFront);

		$this->assertEquals(array($errorMessageAdmin), $flashMessageAdmin->getErrorMessages());
		$this->assertEquals(array(), $flashMessageAdmin->getInfoMessages());
		$this->assertEquals(array($successMessageAdmin), $flashMessageAdmin->getSuccessMessages());
		$this->assertEquals(array($errorMessageFront), $flashMessageFront->getErrorMessages());
		$this->assertEquals(array(), $flashMessageFront->getInfoMessages());
		$this->assertEquals(array(), $flashMessageFront->getSuccessMessages());
	}

	public function testAddArrayOfMessages() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$errorMessagesAdmin = array('First error message admin', 'Second error message admin');

		$flashMessageAdmin->addError($errorMessagesAdmin);

		$this->assertEquals($errorMessagesAdmin, $flashMessageAdmin->getErrorMessages());
	}

	public function testGetUniqMessage() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$errorMessageAdmin = 'Error message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);
		$flashMessageAdmin->addError($errorMessageAdmin);

		$this->assertEquals(array($errorMessageAdmin), $flashMessageAdmin->getErrorMessages());
	}

	public function testGetAndClearBag() {
		$flashMessageAdmin = $this->getContainer()->get('ss6.shop.flash_message.admin');
		/* @var $flashMessageAdmin \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$errorMessageAdmin = 'Error message admin';

		$flashMessageAdmin->addError($errorMessageAdmin);

		$this->assertEquals(array($errorMessageAdmin), $flashMessageAdmin->getErrorMessages());
		$this->assertEquals(array(), $flashMessageAdmin->getErrorMessages());
	}
}
