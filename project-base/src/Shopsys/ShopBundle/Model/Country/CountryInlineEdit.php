<?php

namespace Shopsys\ShopBundle\Model\Country;

use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Country\CountryFormType;
use Shopsys\ShopBundle\Model\Country\CountryData;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Country\CountryGridFactory;
use Symfony\Component\Form\FormFactory;

class CountryInlineEdit extends AbstractGridInlineEdit
{

    /**
     * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
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
     * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
     * @return int
     */
    protected function createEntityAndGetId($countryData) {
        $country = $this->countryFacade->create($countryData, $this->selectedDomain->getId());

        return $country->getId();
    }

    /**
     * @param int $countryId
     * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
     */
    protected function editEntity($countryId, $countryData) {
        $this->countryFacade->edit($countryId, $countryData);
    }

    /**
     * @param int|null $countryId
     * @return \Shopsys\ShopBundle\Model\Country\CountryData
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
     * @return \Shopsys\ShopBundle\Form\Admin\Country\CountryFormType
     */
    protected function getFormType($rowId) {
        return new CountryFormType();
    }

}
