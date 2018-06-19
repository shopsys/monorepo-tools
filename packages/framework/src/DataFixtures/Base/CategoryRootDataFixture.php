<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory;
use Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface;

class CategoryRootDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const CATEGORY_ROOT = 'category_root';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface
     */
    protected $categoryFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory
     */
    protected $categoryDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface $categoryFactory
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory $categoryDataFactory
     */
    public function __construct(
        CategoryFactoryInterface $categoryFactory,
        CategoryDataFactory $categoryDataFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryDataFactory = $categoryDataFactory;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $categoryData = $this->categoryDataFactory->createDefault();
        $rootCategory = $this->categoryFactory->create($categoryData);
        $manager->persist($rootCategory);
        $manager->flush($rootCategory);
        $this->addReference(self::CATEGORY_ROOT, $rootCategory);
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
