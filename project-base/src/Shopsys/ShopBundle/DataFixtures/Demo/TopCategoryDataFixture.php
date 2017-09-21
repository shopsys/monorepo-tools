<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;

class TopCategoryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $topCategoryFacade = $this->get('shopsys.shop.category.top_category.top_category_facade');
        /* @var $topCategoryFacade \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade */

        $categories = [
            $this->getReference(CategoryDataFixture::CATEGORY_ELECTRONICS),
            $this->getReference(CategoryDataFixture::CATEGORY_BOOKS),
            $this->getReference(CategoryDataFixture::CATEGORY_TOYS),
        ];

        $topCategoryFacade->saveTopCategoriesForDomain(Domain::FIRST_DOMAIN_ID, $categories);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            CategoryDataFixture::class,
        ];
    }
}
