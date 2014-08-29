<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Form\Admin\Product\Availability\AvailabilityFormType;
use SS6\ShopBundle\Model\PKGrid\InlineEdit\GridInlineEditInterface;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityGridFactory;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

class AvailabilityInlineEdit implements GridInlineEditInterface {

	/**
	 * @var \Symfony\Component\Form\FormFactory
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade
	 */
	private $availabilityFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityGridFactory
	 */
	private $availabilityGridFactory;

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
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityGridFactory $availabilityGridFactory
	 */
	public function __construct(
		FormFactory $formFactory,
		AvailabilityFacade $availabilityFacade,
		AvailabilityGridFactory $availabilityGridFactory
	) {
		$this->formFactory = $formFactory;
		$this->availabilityFacade = $availabilityFacade;
		$this->availabilityGridFactory = $availabilityGridFactory;

		$this->serviceName = 'ss6.shop.product.availability.availability_inline_edit';
		$this->queryId = 'a.id';
	}

	/**
	 * @param mixed $availabilityId
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm($availabilityId) {
		$availabilityData = new AvailabilityData();

		if ($availabilityId !== null) {
			$availabilityId = (int)$availabilityId;
			$availability = $this->availabilityFacade->getById($availabilityId);
			$availabilityData->setFromEntity($availability);
		}

		return $this->formFactory->create(new AvailabilityFormType(), $availabilityData);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param mixed $availabilityId
	 * @return int
	 * @throws \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException
	 */
	public function saveForm(Request $request, $availabilityId) {
		$form = $this->getForm($availabilityId);
		$form->handleRequest($request);
		
		if (!$form->isValid()) {
			$formErrors = [];
			foreach ($form->getErrors(true) as $error) {
				/* @var $error \Symfony\Component\Form\FormError */
				$formErrors[] = $error->getMessage();
			}
			throw new \SS6\ShopBundle\Model\PKGrid\InlineEdit\Exception\InvalidFormDataException($formErrors);
		}

		$availabilityData = $form->getData();
		if ($availabilityId !== null) {
			$availabilityId = (int)$availabilityId;
			$this->availabilityFacade->edit($availabilityId, $availabilityData);
		} else {
			$availability = $this->availabilityFacade->create($availabilityData);
			$availabilityId = $availability->getId();
		}

		return $availabilityId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function getGrid() {
		$grid = $this->availabilityGridFactory->create();
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
