<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade;

class TopCategoryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $topCategoryFacade = $this->get(TopCategoryFacade::class);
        /* @var $topCategoryFacade \Shopsys\ShopBundle\Model\Category\TopCategory\TopCategoryFacade */

        $categories = [
            $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::ELECTRONICS),
            $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::BOOKS),
            $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::TOYS),
        ];

        $topCategoryFacade->saveTopCategoriesForDomain(Domain::FIRST_DOMAIN_ID, $categories);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies() {
        return [
            CategoryDataFixture::class,
        ];
    }
}
