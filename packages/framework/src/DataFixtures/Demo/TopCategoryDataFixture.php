<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFacade;

class TopCategoryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $topCategoryFacade = $this->get(TopCategoryFacade::class);
        /* @var $topCategoryFacade \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFacade */

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
