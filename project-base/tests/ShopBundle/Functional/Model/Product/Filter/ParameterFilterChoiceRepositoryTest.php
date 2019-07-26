<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Model\Product\Filter;

use Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoice;
use Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoiceRepository;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class ParameterFilterChoiceRepositoryTest extends TransactionFunctionalTestCase
{
    public function testParameterFilterChoicesFromCategoryWithNoParameters(): void
    {
        $parameterFilterChoices = $this->getChoicesForCategoryReference(CategoryDataFixture::CATEGORY_GARDEN_TOOLS);

        $this->assertCount(0, $parameterFilterChoices);
    }

    public function testParameterFilterChoicesFromCategory(): void
    {
        $parameterFilterChoices = $this->getChoicesForCategoryReference(CategoryDataFixture::CATEGORY_BOOKS);

        $this->assertCount(3, $parameterFilterChoices);

        $ids = array_map(
            static function (ParameterFilterChoice $parameterFilterChoice) {
                return $parameterFilterChoice->getParameter()->getId();
            },
            $parameterFilterChoices
        );

        $this->assertContains(50, $ids);
        $this->assertContains(49, $ids);
        $this->assertContains(10, $ids);

        $parameterParameterValuePair = [
            0 => [109, 115],
            1 => [117, 121, 111, 105],
            2 => [113, 119, 107],
        ];

        foreach ($parameterParameterValuePair as $i => $parameterValues) {
            foreach ($parameterValues as $j => $value) {
                $this->assertSame($value, $parameterFilterChoices[$i]->getValues()[$j]->getId());
            }
        }
    }

    /**
     * @param string $categoryReferenceName
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoice[]
     */
    protected function getChoicesForCategoryReference(string $categoryReferenceName): array
    {
        $repository = $this->getParameterFilterChoiceRepository();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Category\Category $category */
        $category = $this->getReference($categoryReferenceName);

        return $repository->getParameterFilterChoicesInCategory(1, $pricingGroup, 'en', $category);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoiceRepository
     */
    public function getParameterFilterChoiceRepository(): ParameterFilterChoiceRepository
    {
        return $this->getContainer()->get(ParameterFilterChoiceRepository::class);
    }
}
