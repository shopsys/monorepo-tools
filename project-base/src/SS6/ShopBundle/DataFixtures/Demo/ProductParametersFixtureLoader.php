<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use SS6\ShopBundle\Model\Product\Parameter\ParameterData;
use SS6\ShopBundle\Model\Product\Parameter\ParameterFacade;
use SS6\ShopBundle\Model\Product\Parameter\ParameterValueData;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;

class ProductParametersFixtureLoader {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade
	 */
	private $parameterFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	private $parameters;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade $parameterFacade
	 */
	public function __construct(ParameterFacade $parameterFacade) {
		$this->parameterFacade = $parameterFacade;
		$this->parameters = [];
	}

	/**
	 * @param string $string
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	public function getProductParameterValuesDataFromString($string, $domainId) {
		$rows = explode(';', $string);

		$productParameterValuesData = [];
		foreach ($rows as $row) {
			$rowData = explode('=', $row);
			if (count($rowData) !== 2) {
				continue;
			}
			list($serializedParameterNames, $serializedValueTexts) = $rowData;
			$serializedParameterNames = trim($serializedParameterNames, '[]');
			$serializedValueTexts = trim($serializedValueTexts, '[]');

			$productParameterValuesData = $this->addProductParameterValuesData(
				$productParameterValuesData,
				$domainId,
				$serializedParameterNames,
				$serializedValueTexts
			);
		}

		return $productParameterValuesData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $productParameterValuesData
	 * @param int $domainId
	 * @param string $serializedParameterNames
	 * @param string $serializedValueTexts
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	private function addProductParameterValuesData(
		array $productParameterValuesData,
		$domainId,
		$serializedParameterNames,
		$serializedValueTexts
	) {
		$csvParameterNames = $this->unserializeLocalizedValues($serializedParameterNames);

		if (!isset($this->parameters[$serializedParameterNames])) {
			$csPrametersNames = [
				'cs' => $csvParameterNames['cs'],
			];
			if ($domainId === 1) {
				$parametersNames = $csPrametersNames;
			} else {
				$parametersNames = $csvParameterNames;
			}
			$parameter = $this->parameterFacade->findParameterByNames($csPrametersNames);
			if ($parameter === null) {
				$parameter = $this->parameterFacade->create(new ParameterData($parametersNames, true));
			}
			$this->parameters[$serializedParameterNames] = $parameter;
		} else {
			$parameter = $this->parameters[$serializedParameterNames];
		}

		if (
			$domainId === 2
			&& array_key_exists('en', $csvParameterNames)
			&& $parameter->getName('en') !== $csvParameterNames['en']
		) {
			$parameterData = new ParameterData();
			$parameterData->setFromEntity($parameter);
			$parameterData->name['en'] = $csvParameterNames['en'];
			$this->parameterFacade->edit($parameter->getId(), $parameterData);
		}

		$valueTexts = $this->unserializeLocalizedValues($serializedValueTexts);
		foreach ($valueTexts as $locale => $valueText) {
			if ($domainId === 1 && $locale === 'en') {
				continue;
			}
			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->parameterValueData = new ParameterValueData($valueText, $locale);
			$productParameterValueData->parameter = $parameter;
			$productParameterValuesData[] = $productParameterValueData;
		}

		return $productParameterValuesData;
	}

	/**
	 * @param string $string
	 * @return string[locale]
	 */
	private function unserializeLocalizedValues($string) {
		$array = [];
		$items = explode(',', $string);
		foreach ($items as $item) {
			list($locale, $value) = explode(':', $item);
			$array[$locale] = $value;
		}
		return $array;
	}

}
