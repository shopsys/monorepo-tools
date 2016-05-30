<?php

namespace SS6\ShopBundle\Tests\Unit\Form;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @UglyTest
 */
class DomainsTypeTest extends FunctionalTestCase {

	public function testGetDataReturnsCorrectArray() {
		$form = $this->getForm();

		$form->setData([1, 2]);
		$this->assertSame([1, 2], $form->getData());
	}

	public function testGetDataAfterSubmitReturnsCorrectArray() {
		$form = $this->getForm();

		$form->submit(['1', '2']);
		$this->assertSame([1, 2], $form->getData());
	}

	public function testSetDataAcceptsNull() {
		$form = $this->getForm();

		$form->setData(null);
		$this->assertSame(null, $form->getData());
	}

	/**
	 * @return \Symfony\Component\Form\FormInterface
	 */
	private function getForm() {
		$formFactory = $this->getContainer()->get(FormFactoryInterface::class);
		/* @var $formFactory \Symfony\Component\Form\FormFactoryInterface */

		return $formFactory->create(FormType::DOMAINS);
	}

}
