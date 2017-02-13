<?php

namespace Shopsys\ShopBundle\Tests\Unit\Form;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class YesNoTypeTest extends FunctionalTestCase
{
    public function testGetDataReturnsTrue()
    {
        $form = $this->getForm();

        $form->setData(true);
        $this->assertSame(true, $form->getData());
    }

    public function testGetDataReturnsFalse()
    {
        $form = $this->getForm();

        $form->setData(false);
        $this->assertSame(false, $form->getData());
    }

    public function testGetDataReturnsTrueAfterSubmit()
    {
        $form = $this->getForm();

        $form->submit('1');
        $this->assertSame(true, $form->getData());
    }

    public function testGetDataReturnsFalseAfterSubmit()
    {
        $form = $this->getForm();

        $form->submit('0');
        $this->assertSame(false, $form->getData());
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    private function getForm()
    {
        $formFactory = $this->getContainer()->get(FormFactoryInterface::class);
        /* @var $formFactory \Symfony\Component\Form\FormFactoryInterface */

        return $formFactory->create(FormType::YES_NO);
    }
}
