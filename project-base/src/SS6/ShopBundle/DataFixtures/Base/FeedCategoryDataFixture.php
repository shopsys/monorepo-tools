<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade;

class FeedCategoryDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$feedCategoryLoaderFacade = $this->get(FeedCategoryLoaderFacade::class);
		/* @var $feedCategoryLoaderFacade \SS6\ShopBundle\Model\Feed\Category\FeedCategoryLoaderFacade */

		try {
			$feedCategoryLoaderFacade->download();
		} catch (\SS6\ShopBundle\Model\Feed\Category\Exception\FeedCategoryLoadException $ex) {
			$feedCategoryLoaderFacade->loadFromBackupFile();
		}
	}

}
