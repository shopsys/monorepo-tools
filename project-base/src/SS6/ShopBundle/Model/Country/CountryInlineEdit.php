<?php

namespace SS6\ShopBundle\Model\Country;

use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Form\Admin\Country\CountryFormType;
use SS6\ShopBundle\Model\Country\CountryData;
use SS6\ShopBundle\Model\Country\CountryFacade;
use SS6\ShopBundle\Model\Country\CountryGridFactory;
use Symfony\Component\Form\FormFactory;

class CountryInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Country\CountryFacade
	 */
	private $countryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	public function __construct(
		FormFactory $formFactory,
		CountryGridFactory $countryGridFactory,
		CountryFacade $countryFacade,
		SelectedDomain $selectedDomain
	) {
		$this->countryFacade = $countryFacade;
		$this->selectedDomain = $selectedDomain;

		parent::__construct($formFactory, $countryGridFactory);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 * @return int
	 */
	protected function createEntityAndGetId($countryData) {
		$country = $this->countryFacade->create($countryData, $this->selectedDomain->getId());

		return $country->getId();
	}

	/**
	 * @param int $countryId
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 */
	protected function editEntity($countryId, $countryData) {
		$this->countryFacade->edit($countryId, $countryData);
	}

	/**
	 * @param int|null $countryId
	 * @return \SS6\ShopBundle\Model\Country\CountryData
	 */
	protected function getFormDataObject($countryId = null) {
		$countryData = new CountryData();

		if ($countryId !== null) {
			$countryId = (int)$countryId;
			$country = $this->countryFacade->getById($countryId);
			$countryData->setFromEntity($country);
		}

		return $countryData;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Form\Admin\Country\CountryFormType
	 */
	protected function getFormType($rowId) {
		return new CountryFormType();
	}

}
