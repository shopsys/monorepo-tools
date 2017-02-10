<?php

namespace Shopsys\ShopBundle\Model\Product\Availability;

use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Product\Availability\AvailabilityFormType;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityGridFactory;
use Symfony\Component\Form\FormFactory;

class AvailabilityInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade
	 */
	private $availabilityFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityGridFactory $availabilityGridFactory
	 * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
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
	 * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 * @return int
	 */
	protected function createEntityAndGetId($availabilityData) {
		$availability = $this->availabilityFacade->create($availabilityData);

		return $availability->getId();
	}

	/**
	 * @param int $availabilityId
	 * @param \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData $availabilityData
	 */
	protected function editEntity($availabilityId, $availabilityData) {
		$this->availabilityFacade->edit($availabilityId, $availabilityData);
	}

	/**
	 * @param int|null $availabilityId
	 * @return \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData
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
	 * @param int $rowId
	 * @return \Shopsys\ShopBundle\Form\Admin\Product\Availability\AvailabilityFormType
	 */
	protected function getFormType($rowId) {
		return new AvailabilityFormType();
	}

}
