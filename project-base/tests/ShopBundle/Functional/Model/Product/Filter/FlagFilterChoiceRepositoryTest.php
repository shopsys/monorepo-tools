<?php

namespace Tests\ShopBundle\Functional\Model\Product\Filter;

use Shopsys\FrameworkBundle\Model\Product\Filter\FlagFilterChoiceRepository;
use Shopsys\FrameworkBundle\Model\Product\Flag\Flag;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class FlagFilterChoiceRepositoryTest extends TransactionFunctionalTestCase
{
    public function testFlagFilterChoicesFromCategoryWithNoFlags(): void
    {
        $flagFilterChoices = $this->getChoicesForCategoryReference(CategoryDataFixture::CATEGORY_GARDEN_TOOLS);

        $this->assertCount(0, $flagFilterChoices);
    }

    public function testFlagFilterChoicesFromCategoryWithFlags(): void
    {
        $flagFilterChoices = $this->getChoicesForCategoryReference(CategoryDataFixture::CATEGORY_ELECTRONICS);

        $this->assertCount(2, $flagFilterChoices);

        $ids = array_map(
            static function (Flag $flag) {
                return $flag->getId();
            },
            $flagFilterChoices
        );

        $this->assertContains(2, $ids);
        $this->assertContains(3, $ids);
    }

    public function testGetFlagFilterChoicesForSearchPhone(): void
    {
        $flagFilterChoices = $this->getChoicesForSearchText('phone');

        $this->assertCount(3, $flagFilterChoices);

        $ids = array_map(
            static function (Flag $flag) {
                return $flag->getId();
            },
            $flagFilterChoices
        );

        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
        $this->assertContains(3, $ids);
    }

    public function testGetFlagFilterChoicesForBook(): void
    {
        $flagFilterChoices = $this->getChoicesForSearchText('book');

        $this->assertCount(2, $flagFilterChoices);

        $ids = array_map(
            static function (Flag $flag) {
                return $flag->getId();
            },
            $flagFilterChoices
        );

        $this->assertContains(1, $ids);
        $this->assertContains(2, $ids);
    }

    /**
     * @param string $categoryReferenceName
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    protected function getChoicesForCategoryReference(string $categoryReferenceName): array
    {
        $repository = $this->getFlagFilterChoiceRepository();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Category\Category $category */
        $category = $this->getReference($categoryReferenceName);

        return $repository->getFlagFilterChoicesInCategory(1, $pricingGroup, 'en', $category);
    }

    /**
     * @param string $searchText
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    protected function getChoicesForSearchText(string $searchText): array
    {
        $repository = $this->getFlagFilterChoiceRepository();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        return $repository->getFlagFilterChoicesForSearch(1, $pricingGroup, 'en', $searchText);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\FlagFilterChoiceRepository
     */
    public function getFlagFilterChoiceRepository(): FlagFilterChoiceRepository
    {
        return $this->getContainer()->get(FlagFilterChoiceRepository::class);
    }
}
