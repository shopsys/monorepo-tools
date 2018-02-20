<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

use SimpleXMLElement;

class HeurekaCategoryDownloader
{
    /**
     * @var string
     */
    private $heurekaCategoryFeedUrl;

    /**
     * @param string $heurekaCategoryFeedUrl
     */
    public function __construct($heurekaCategoryFeedUrl)
    {
        $this->heurekaCategoryFeedUrl = $heurekaCategoryFeedUrl;
    }

    /**
     * @return array[]
     */
    public function getHeurekaCategories()
    {
        $categoryDataObjects = $this->loadXml()->xpath('/HEUREKA//CATEGORY[CATEGORY_FULLNAME]');

        return $this->convertObjectsToArrays($categoryDataObjects);
    }

    /**
     * @return \SimpleXMLElement
     */
    private function loadXml()
    {
        try {
            return new SimpleXMLElement($this->heurekaCategoryFeedUrl, LIBXML_NOERROR | LIBXML_NOWARNING, true);
        } catch (\Exception $e) {
            throw new \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryDownloadFailedException($e);
        }
    }

    /**
     * @param \SimpleXMLElement[] $categoryDataObjects
     * @return array[]
     */
    private function convertObjectsToArrays(array $categoryDataObjects)
    {
        $categoryDataArrays = [];

        foreach ($categoryDataObjects as $categoryDataObject) {
            $categoryId = (int)$categoryDataObject->CATEGORY_ID;

            $categoryDataArrays[$categoryId] = [
                'id' => $categoryId,
                'name' => (string)$categoryDataObject->CATEGORY_NAME,
                'full_name' => (string)$categoryDataObject->CATEGORY_FULLNAME,
            ];
        }

        return $categoryDataArrays;
    }
}
