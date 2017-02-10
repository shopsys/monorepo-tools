<?php

namespace Shopsys\ShopBundle\Form\Front\Product;

use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Product\Filter\BrandFilterChoiceRepository;
use Shopsys\ShopBundle\Model\Product\Filter\FlagFilterChoiceRepository;
use Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterChoiceRepository;
use Shopsys\ShopBundle\Model\Product\Filter\PriceRangeRepository;

class ProductFilterFormTypeFactory
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterChoiceRepository
     */
    private $parameterFilterChoiceRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\FlagFilterChoiceRepository
     */
    private $flagFilterChoiceRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\BrandFilterChoiceRepository
     */
    private $brandFilterChoiceRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\PriceRangeRepository
     */
    private $priceRangeRepository;

    public function __construct(
        ParameterFilterChoiceRepository $parameterFilterChoiceRepository,
        FlagFilterChoiceRepository $flagFilterChoiceRepository,
        CurrentCustomer $currentCustomer,
        BrandFilterChoiceRepository $brandFilterChoiceRepository,
        PriceRangeRepository $priceRangeRepository
    ) {
        $this->parameterFilterChoiceRepository = $parameterFilterChoiceRepository;
        $this->flagFilterChoiceRepository = $flagFilterChoiceRepository;
        $this->currentCustomer = $currentCustomer;
        $this->brandFilterChoiceRepository = $brandFilterChoiceRepository;
        $this->priceRangeRepository = $priceRangeRepository;
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType
     */
    public function createForCategory($domainId, $locale, Category $category) {
        $pricingGroup = $this->currentCustomer->getPricingGroup();
        $parameterFilterChoices = $this->parameterFilterChoiceRepository
            ->getParameterFilterChoicesInCategory($domainId, $pricingGroup, $locale, $category);
        $flagFilterChoices = $this->flagFilterChoiceRepository
            ->getFlagFilterChoicesInCategory($domainId, $pricingGroup, $locale, $category);
        $brandFilterChoices = $this->brandFilterChoiceRepository
            ->getBrandFilterChoicesInCategory($domainId, $pricingGroup, $category);
        $priceRange = $this->priceRangeRepository->getPriceRangeInCategory($domainId, $pricingGroup, $category);

        return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices, $brandFilterChoices, $priceRange);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param string|null $searchText
     * @return \Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType
     */
    public function createForSearch($domainId, $locale, $searchText) {
        $parameterFilterChoices = [];
        $pricingGroup = $this->currentCustomer->getPricingGroup();
        $flagFilterChoices = $this->flagFilterChoiceRepository
            ->getFlagFilterChoicesForSearch($domainId, $pricingGroup, $locale, $searchText);
        $brandFilterChoices = $this->brandFilterChoiceRepository
            ->getBrandFilterChoicesForSearch($domainId, $pricingGroup, $locale, $searchText);
        $priceRange = $this->priceRangeRepository->getPriceRangeForSearch($domainId, $pricingGroup, $locale, $searchText);

        return new ProductFilterFormType($parameterFilterChoices, $flagFilterChoices, $brandFilterChoices, $priceRange);
    }
}
