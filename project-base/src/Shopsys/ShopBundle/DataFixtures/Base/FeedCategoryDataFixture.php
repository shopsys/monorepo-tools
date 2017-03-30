<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade;

class FeedCategoryDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $feedCategoryLoaderFacade = $this->get('shopsys.shop.feed.category.feed_category_loader_facade');
        /* @var $feedCategoryLoaderFacade \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade */

        try {
            $feedCategoryLoaderFacade->download();
        } catch (\Shopsys\ShopBundle\Model\Feed\Category\Exception\FeedCategoryLoadException $ex) {
            $feedCategoryLoaderFacade->loadFromBackupFile();
        }
    }
}
