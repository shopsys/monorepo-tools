<?php

namespace SS6\ShopBundle\Model\Pricing\Vat;

use SS6\ShopBundle\Form\Admin\Vat\VatFormType;
use SS6\ShopBundle\Model\PKGrid\InlineEdit\GridInlineEditInterface;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Pricing\Vat\VatGridFactory;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

class VatInlineEdit implements GridInlineEditInterface {

	/**
	 * @var \Symfony\Component\Form\FormFactory
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatGridFactory
	 */
	private $vatGridFactory;

	/**
	 * @var string
	 */
	private $serviceName;

	/**
	 * @var string
	 */
	private $queryId;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatFacade $vatFacade
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatGridFactory $vatGridFactory
	 */
	public function __construct(
		FormFactory $formFactory,
		VatFacade $vatFacade,
		VatGridFactory $vatGridFactory
	) {
		$this->formFactory = $formFactory;
		$this->vatFacade = $vatFacade;
		$this->vatGridFactory = $vatGridFactory;

		$this->serviceName = 'ss6.shop.pricing.vat.vat_inline_edit';
		$this->queryId = 'v.id';
	}

	/**
	 * @param mixed $vatId
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm($vatId) {
		$vatData = new VatData();

		if ($vatId !== null) {
			$vatId = (int)$vatId;
			$vat = $this->vatFacade->getById($vatId);
			$vatData->setFromEntity($vat);
		}

		return $this->formFactory->create(new VatFormType(), $vatData);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param mixed $vatId
	 * @return int
	 * @throws \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException
	 */
	public function saveForm(Request $request, $vatId) {
		$form = $this->getForm($vatId);
		$form->handleRequest($request);
		
		if (!$form->isValid()) {
			$formErrors = [];
			foreach ($form->getErrors(true) as $error) {
				/* @var $error \Symfony\Component\Form\FormError */
				$formErrors[] = $error->getMessage();
			}
			throw new \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException($formErrors);
		}

		$vatData = $form->getData();
		if ($vatId !== null) {
			$vatId = (int)$vatId;
			$this->vatFacade->edit($vatId, $vatData);
		} else {
			$vat = $this->vatFacade->create($vatData);
			$vatId = $vat->getId();
		}

		return $vatId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function getGrid() {
		$grid = $this->vatGridFactory->create();
		$grid->setInlineEditService($this);

		return $grid;
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return $this->serviceName;
	}

	/**
	 * @return string
	 */
	public function getQueryId() {
		return $this->queryId;
	}

}
