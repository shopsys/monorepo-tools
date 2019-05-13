<?php

namespace Tests\ShopBundle\Functional\Model\Product\Filter;

use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Filter\BrandFilterChoiceRepository;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class BrandFilterChoiceRepositoryTest extends TransactionFunctionalTestCase
{
    public function testBrandFilterChoicesFromCategoryWithNoBrands(): void
    {
        $brandFilterChoices = $this->getChoicesForCategoryReference(CategoryDataFixture::CATEGORY_BOOKS);

        $this->assertCount(0, $brandFilterChoices);
    }

    public function testBrandFilterChoicesFromCategoryWithBrands(): void
    {
        $brandFilterChoices = $this->getChoicesForCategoryReference(CategoryDataFixture::CATEGORY_ELECTRONICS);

        $this->assertCount(4, $brandFilterChoices);

        $ids = array_map(
            static function (Brand $brand) {
                return $brand->getId();
            },
            $brandFilterChoices
        );

        $this->assertContains(4, $ids);
        $this->assertContains(6, $ids);
        $this->assertContains(3, $ids);
        $this->assertContains(5, $ids);
    }

    public function testGetBrandFilterChoicesForSearchPhone(): void
    {
        $brandFilterChoices = $this->getChoicesForSearchText('phone');

        $this->assertCount(7, $brandFilterChoices);

        $ids = array_map(
            static function (Brand $brand) {
                return $brand->getId();
            },
            $brandFilterChoices
        );

        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
        $this->assertContains(15, $ids);
        $this->assertContains(3, $ids);
        $this->assertContains(4, $ids);
        $this->assertContains(20, $ids);
        $this->assertContains(19, $ids);
    }

    public function testGetBrandFilterChoicesForSearch47(): void
    {
        $brandFilterChoices = $this->getChoicesForSearchText('47');

        $this->assertCount(1, $brandFilterChoices);

        $this->assertSame(3, $brandFilterChoices[0]->getId());
    }

    /**
     * @param string $categoryReferenceName
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    protected function getChoicesForCategoryReference(string $categoryReferenceName): array
    {
        $repository = $this->getBrandFilterChoiceRepository();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Category\Category $category */
        $category = $this->getReference($categoryReferenceName);

        return $repository->getBrandFilterChoicesInCategory(1, $pricingGroup, $category);
    }

    /**
     * @param string $searchText
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    protected function getChoicesForSearchText(string $searchText): array
    {
        $repository = $this->getBrandFilterChoiceRepository();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        return $repository->getBrandFilterChoicesForSearch(1, $pricingGroup, 'en', $searchText);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\BrandFilterChoiceRepository
     */
    public function getBrandFilterChoiceRepository(): BrandFilterChoiceRepository
    {
        return $this->getContainer()->get(BrandFilterChoiceRepository::class);
    }
}
