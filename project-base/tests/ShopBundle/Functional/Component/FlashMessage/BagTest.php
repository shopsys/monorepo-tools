<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Component\FlashMessage;

use Tests\ShopBundle\Test\FunctionalTestCase;

class BagTest extends FunctionalTestCase
{
    public function testAddFrontVsAdmin()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\Bag $flashMessageAdmin */
        $flashMessageAdmin = $this->getContainer()->get('shopsys.shop.component.flash_message.bag.admin');
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\Bag $flashMessageFront */
        $flashMessageFront = $this->getContainer()->get('shopsys.shop.component.flash_message.bag.front');

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

    public function testAddArrayOfMessages()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\Bag $flashMessageAdmin */
        $flashMessageAdmin = $this->getContainer()->get('shopsys.shop.component.flash_message.bag.admin');

        $errorMessagesAdmin = ['First error message admin', 'Second error message admin'];

        $flashMessageAdmin->addError($errorMessagesAdmin);

        $this->assertSame($errorMessagesAdmin, $flashMessageAdmin->getErrorMessages());
    }

    public function testGetUniqueMessage()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\Bag $flashMessageAdmin */
        $flashMessageAdmin = $this->getContainer()->get('shopsys.shop.component.flash_message.bag.admin');

        $errorMessageAdmin = 'Error message admin';

        $flashMessageAdmin->addError($errorMessageAdmin);
        $flashMessageAdmin->addError($errorMessageAdmin);

        $this->assertSame([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
    }

    public function testGetAndClearBag()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\Bag $flashMessageAdmin */
        $flashMessageAdmin = $this->getContainer()->get('shopsys.shop.component.flash_message.bag.admin');

        $errorMessageAdmin = 'Error message admin';

        $flashMessageAdmin->addError($errorMessageAdmin);

        $this->assertSame([$errorMessageAdmin], $flashMessageAdmin->getErrorMessages());
        $this->assertSame([], $flashMessageAdmin->getErrorMessages());
    }

    public function testIsEmpty()
    {
        /** @var \Shopsys\FrameworkBundle\Component\FlashMessage\Bag $flashMessageAdmin */
        $flashMessageAdmin = $this->getContainer()->get('shopsys.shop.component.flash_message.bag.admin');

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
