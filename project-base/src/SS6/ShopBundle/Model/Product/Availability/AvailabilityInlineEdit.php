<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Form\Admin\Product\Availability\AvailabilityFormType;
use SS6\ShopBundle\Model\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityGridFactory;
use Symfony\Component\Form\FormFactory;

class AvailabilityInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade
	 */
	private $availabilityFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityGridFactory $availabilityGridFactory
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		AvailabilityGridFactory $availabilityGridFactory,
		AvailabilityFacade $availabilityFacade
	) {
		$this->availabilityFacade = $availabilityFacade;

		parent::__construct($formFactory, $availabilityGridFactory);
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return 'ss6.shop.product.availability.availability_inline_edit';
	}

	/**
	 * @return string
	 */
	public function getQueryId() {
		return 'a.id';
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 * @return int
	 */
	protected function createEntityAndGetId($availabilityData) {
		$availability = $this->availabilityFacade->create($availabilityData);

		return $availability->getId();
	}

	/**
	 * @param int $availabilityId
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 */
	protected function editEntity($availabilityId, $availabilityData) {
		$this->availabilityFacade->edit($availabilityId, $availabilityData);
	}

	/**
	 * @param int|null $availabilityId
	 * @return \SS6\ShopBundle\Model\Product\Availability\AvailabilityData
	 */
	protected function getFormDataObject($availabilityId = null) {
		$availabilityData = new AvailabilityData();

		if ($availabilityId !== null) {
			$availabilityId = (int)$availabilityId;
			$availability = $this->availabilityFacade->getById($availabilityId);
			$availabilityData->setFromEntity($availability);
		}

		return $availabilityData;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Product\Availability\AvailabilityFormType
	 */
	protected function getFormType() {
		return new AvailabilityFormType();
	}

}
