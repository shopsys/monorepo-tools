<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\CategoryData;
use Shopsys\FrameworkBundle\Model\Category\CategoryDomainFactoryInterface;
use Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface;

class CategoryRootDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const CATEGORY_ROOT = 'category_root';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface
     */
    protected $categoryFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryDomainFactoryInterface
     */
    protected $categoryDomainFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface $categoryFactory
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryDomainFactoryInterface $categoryDomainFactory
     */
    public function __construct(
        CategoryFactoryInterface $categoryFactory,
        CategoryDomainFactoryInterface $categoryDomainFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryDomainFactory = $categoryDomainFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $rootCategory = $this->categoryFactory->create(new CategoryData());
        $manager->persist($rootCategory);
        $manager->flush($rootCategory);
        $this->addReference(self::CATEGORY_ROOT, $rootCategory);

        $categoryDomain = $this->categoryDomainFactory->create($rootCategory, Domain::FIRST_DOMAIN_ID);
        $manager->persist($categoryDomain);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            SettingValueDataFixture::class,
        ];
    }
}
