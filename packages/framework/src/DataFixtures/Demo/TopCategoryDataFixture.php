<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFacade;

class TopCategoryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /** @var \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFacade */
    private $topCategoryFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategoryFacade $topCategoryFacade
     */
    public function __construct(TopCategoryFacade $topCategoryFacade)
    {
        $this->topCategoryFacade = $topCategoryFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $categories = [
            $this->getReference(CategoryDataFixture::CATEGORY_ELECTRONICS),
            $this->getReference(CategoryDataFixture::CATEGORY_BOOKS),
            $this->getReference(CategoryDataFixture::CATEGORY_TOYS),
        ];

        $this->topCategoryFacade->saveTopCategoriesForDomain(Domain::FIRST_DOMAIN_ID, $categories);
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
