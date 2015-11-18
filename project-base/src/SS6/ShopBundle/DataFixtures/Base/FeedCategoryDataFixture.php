<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryDownloadFacade;

class FeedCategoryDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$feedCategoryDownloadFacade = $this->get(FeedCategoryDownloadFacade::class);
		/* @var $feedCategoryDownloadFacade \SS6\ShopBundle\Model\Feed\Category\FeedCategoryDownloadFacade */

		try {
			$feedCategoryDownloadFacade->download();
		} catch (\SS6\ShopBundle\Model\Feed\Category\Exception\FeedCategoryLoadException $ex) {
			$feedCategoryDownloadFacade->loadFromBackupFile();
		}
	}

}
