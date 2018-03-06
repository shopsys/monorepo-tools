<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryData;
use Shopsys\FrameworkBundle\Model\Category\CategoryDomain;

class CategoryRootDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const CATEGORY_ROOT = 'category_root';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $rootCategory = new Category(new CategoryData());
        $manager->persist($rootCategory);
        $manager->flush($rootCategory);
        $this->addReference(self::CATEGORY_ROOT, $rootCategory);

        $categoryDomain = new CategoryDomain($rootCategory, Domain::FIRST_DOMAIN_ID);
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
