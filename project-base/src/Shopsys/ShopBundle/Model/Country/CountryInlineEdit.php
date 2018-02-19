<?php

namespace Shopsys\ShopBundle\Model\Country;

use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Component\Grid\InlineEdit\AbstractGridInlineEdit;
use Shopsys\ShopBundle\Form\Admin\Country\CountryFormType;
use Symfony\Component\Form\FormFactory;

class CountryInlineEdit extends AbstractGridInlineEdit
{
    /**
     * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;

    public function __construct(
        CountryGridFactory $countryGridFactory,
        CountryFacade $countryFacade,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        FormFactory $formFactory
    ) {
        parent::__construct($countryGridFactory);
        $this->countryFacade = $countryFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
     * @return int
     */
    protected function createEntityAndGetId($countryData)
    {
        $country = $this->countryFacade->create($countryData, $this->adminDomainTabsFacade->getSelectedDomainId());

        return $country->getId();
    }

    /**
     * @param int $countryId
     * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
     */
    protected function editEntity($countryId, $countryData)
    {
        $this->countryFacade->edit($countryId, $countryData);
    }

    /**
     * @param int|null $countryId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getForm($countryId)
    {
        $countryData = new CountryData();

        if ($countryId !== null) {
            $country = $this->countryFacade->getById((int)$countryId);
            $countryData->setFromEntity($country);
        }

        return $this->formFactory->create(CountryFormType::class, $countryData);
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return self::class;
    }
}
