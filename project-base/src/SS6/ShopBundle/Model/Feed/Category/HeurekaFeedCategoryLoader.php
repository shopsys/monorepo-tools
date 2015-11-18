<?php

namespace SS6\ShopBundle\Model\Feed\Category;

use SimpleXMLElement;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryData;

class HeurekaFeedCategoryLoader {

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Category\FeedCategoryData[]
	 */
	public function load($heurekaCategoryFeedUrlOrFilePath) {
		$feedCategoriesData = [];

		try {
			$xml = new SimpleXMLElement($heurekaCategoryFeedUrlOrFilePath, LIBXML_NOERROR | LIBXML_NOWARNING, true);
		} catch (\Exception $ex) {
			throw new \SS6\ShopBundle\Model\Feed\Category\Exception\FeedCategoryLoadException('Cannot load feed categories XML', $ex);
		}

		$xmlCategoriesWithFullName = $xml->xpath('/HEUREKA//CATEGORY[CATEGORY_FULLNAME]');

		foreach ($xmlCategoriesWithFullName as $xmlCategory) {
			$feedCategoryData = new FeedCategoryData();
			$feedCategoryData->extId = (int)$xmlCategory->CATEGORY_ID;
			$feedCategoryData->name = (string)$xmlCategory->CATEGORY_NAME;
			$feedCategoryData->fullName = (string)$xmlCategory->CATEGORY_FULLNAME;

			$feedCategoriesData[] = $feedCategoryData;
		}

		return $feedCategoriesData;
	}

}
