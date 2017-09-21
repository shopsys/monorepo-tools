<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Collections\ArrayCollection;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterData;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade;
use Shopsys\ShopBundle\Model\Product\Parameter\ParameterValueData;
use Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData;

class ProductParametersFixtureLoader
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade
     */
    private $parameterFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\Parameter[]
     */
    private $parameters;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ParameterFacade $parameterFacade
     */
    public function __construct(ParameterFacade $parameterFacade)
    {
        $this->parameterFacade = $parameterFacade;
        $this->parameters = [];
    }

    /**
     * @param string|null $cellValue
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    public function getProductParameterValuesDataFromString($cellValue)
    {
        if ($cellValue === null) {
            return [];
        }

        $parameterRows = explode(';', $cellValue);
        $productParameterValuesDataCollection = new ArrayCollection();
        foreach ($parameterRows as $parameterRow) {
            list($serializedParameterNames, $serializedValueTexts) = explode('=', $parameterRow);

            $this->addProductParameterValuesDataToCollection(
                $productParameterValuesDataCollection,
                trim($serializedParameterNames, '[]'),
                trim($serializedValueTexts, '[]')
            );
        }

        return $productParameterValuesDataCollection->toArray();
    }

    public function clearCache()
    {
        $this->parameters = [];
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $productParameterValuesDataCollection
     * @param string $serializedParameterNames
     * @param string $serializedValueTexts
     */
    private function addProductParameterValuesDataToCollection(
        ArrayCollection $productParameterValuesDataCollection,
        $serializedParameterNames,
        $serializedValueTexts
    ) {
        $parameterNames = $this->getDeserializedValuesIndexedByLocale($serializedParameterNames);
        $parameterValues = $this->getDeserializedValuesIndexedByLocale($serializedValueTexts);

        $parameter = $this->findParameterByNamesOrCreateNew($parameterNames);

        foreach ($parameterValues as $locale => $parameterValue) {
            $productParameterValueData = new ProductParameterValueData();
            $productParameterValueData->parameterValueData = new ParameterValueData($parameterValue, $locale);
            $productParameterValueData->parameter = $parameter;

            $productParameterValuesDataCollection->add($productParameterValueData);
        }
    }

    /**
     * @param string $serializedString
     * @return string[]
     */
    private function getDeserializedValuesIndexedByLocale($serializedString)
    {
        $values = [];
        $items = explode(',', $serializedString);
        foreach ($items as $item) {
            list($locale, $value) = explode(':', $item);
            $values[$locale] = $value;
        }

        return $values;
    }

    /**
     * @param string[] $parameterNamesByLocale
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\Parameter
     */
    private function findParameterByNamesOrCreateNew(array $parameterNamesByLocale)
    {
        $cacheId = json_encode($parameterNamesByLocale);

        if (isset($this->parameters[$cacheId])) {
            return $this->parameters[$cacheId];
        }

        $parameter = $this->parameterFacade->findParameterByNames($parameterNamesByLocale);

        if ($parameter === null) {
            $visible = true;
            $parameterData = new ParameterData($parameterNamesByLocale, $visible);
            $parameter = $this->parameterFacade->create($parameterData);
        }

        $this->parameters[$cacheId] = $parameter;

        return $parameter;
    }
}
