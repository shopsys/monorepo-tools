<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryData;
use Shopsys\ShopBundle\Model\Category\CategoryDomain;

class CategoryRootDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{

    const ROOT = 'category_root';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $rootCategory = new Category(new CategoryData());
        $manager->persist($rootCategory);
        $manager->flush($rootCategory);
        $this->addReference(self::ROOT, $rootCategory);

        $categoryDomain = new CategoryDomain($rootCategory, Domain::FIRST_DOMAIN_ID);
        $manager->persist($categoryDomain);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies() {
        return [
            SettingValueDataFixture::class,
        ];
    }
}
