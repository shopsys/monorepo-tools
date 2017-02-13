<?php

namespace Shopsys\ShopBundle\Form\Admin\AdvancedSearch;

class AdvancedSearchFilterTranslation
{
    /**
     * @var string[filterName]
     */
    private $filtersTranslations;

    public function __construct()
    {
        $this->filtersTranslations = [];
    }

    /**
     * @param string $filterName
     * @param string $filterTranslation
     */
    public function addFilterTranslation($filterName, $filterTranslation)
    {
        $this->filtersTranslations[$filterName] = $filterTranslation;
    }

    /**
     * @param string $filterName
     * @return string
     */
    public function translateFilterName($filterName)
    {
        if (array_key_exists($filterName, $this->filtersTranslations)) {
            return $this->filtersTranslations[$filterName];
        }

        $message = 'Filter "' . $filterName . '" translation not found.';
        throw new \Shopsys\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchTranslationNotFoundException($message);
    }
}
