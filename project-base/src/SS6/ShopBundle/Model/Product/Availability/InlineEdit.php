<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Form\Admin\Product\Availability\AvailabilityFormType;
use SS6\ShopBundle\Model\PKGrid\InlineEdit\GridInlineEditInterface;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Symfony\Component\Form\FormFactory;

class InlineEdit implements GridInlineEditInterface {

	/**
	 * @var \Symfony\Component\Form\FormFactory
	 */
	private $formFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade
	 */
	private $availabilityFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade
	 */
	public function __construct(FormFactory $formFactory, AvailabilityFacade $availabilityFacade) {
		$this->formFactory = $formFactory;
		$this->availabilityFacade = $availabilityFacade;
	}

	/**
	 * @param mixed $availabilityId
	 * @return \Symfony\Component\Form\Form
	 */
	public function getForm($availabilityId) {
		$availabilityId = (int)$availabilityId;
		$availability = $this->availabilityFacade->getById($availabilityId);
		
		$availabilityData = new AvailabilityData();
		$availabilityData->setFromEntity($availability);

		return $this->formFactory->create(new AvailabilityFormType(), $availabilityData);
	}
	
}
