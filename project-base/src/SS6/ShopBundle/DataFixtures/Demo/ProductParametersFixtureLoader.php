<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Collections\ArrayCollection;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Product\Parameter\Parameter;
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
		$parameterRows = explode(';', $string);
		$productParameterValuesDataCollection = new ArrayCollection();
		foreach ($parameterRows as $parameterRow) {
			$parameterRowData = explode('=', $parameterRow);
			if (count($parameterRowData) !== 2) {
				continue;
			}

			list($serializedParameterNames, $serializedValueTexts) = $parameterRowData;

			$this->addProductParameterValuesDataToCollection(
				$productParameterValuesDataCollection,
				$domainId,
				trim($serializedParameterNames, '[]'),
				trim($serializedValueTexts, '[]')
			);
		}

		return $productParameterValuesDataCollection->toArray();
	}

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $productParameterValuesDataCollection
	 * @param int $domainId
	 * @param string $serializedParameterNames
	 * @param string $serializedValueTexts
	 */
	private function addProductParameterValuesDataToCollection(
		ArrayCollection $productParameterValuesDataCollection,
		$domainId,
		$serializedParameterNames,
		$serializedValueTexts
	) {
		$parameterNames = $this->deserializeLocalizedValues($serializedParameterNames);
		$parameter = $this->getParameter($domainId, $serializedParameterNames, $parameterNames);

		$isSecondDomain = $domainId === 2;
		$existsEnglishLocale = array_key_exists('en', $parameterNames);
		if ($isSecondDomain	&& $existsEnglishLocale	&& $parameter->getName('en') !== $parameterNames['en']) {
			$this->addEnglishTranslationToParameter($parameter, $parameterNames);
		}

		$parameterValues = $this->deserializeLocalizedValues($serializedValueTexts);
		foreach ($parameterValues as $locale => $parameterValue) {
			if ($domainId === Domain::FIRST_DOMAIN_ID && $locale === 'en') {
				continue;
			}

			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->parameterValueData = new ParameterValueData($parameterValue, $locale);
			$productParameterValueData->parameter = $parameter;

			$productParameterValuesDataCollection->add($productParameterValueData);
		}
	}

	/**
	 * @param string $serializedString
	 * @return string[locale]
	 */
	private function deserializeLocalizedValues($serializedString) {
		$array = [];
		$items = explode(',', $serializedString);
		foreach ($items as $item) {
			list($locale, $value) = explode(':', $item, 2);
			$array[$locale] = $value;
		}

		return $array;
	}

	/**
	 * @param int $domainId
	 * @param string $serializedParameterNames
	 * @param string[] $parameterNames
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	private function getParameter($domainId, $serializedParameterNames, array $parameterNames) {
		if (isset($this->parameters[$serializedParameterNames])) {
			return $this->parameters[$serializedParameterNames];
		}

		$czechParameterNames = $this->getCzechParameterNamesFromCsvParameterNames($parameterNames);
		$allParameterNames = $domainId === Domain::FIRST_DOMAIN_ID ? $czechParameterNames : $parameterNames;
		$parameter = $this->findParameterByCzechNamesOrCreateNewByAllNames($czechParameterNames, $allParameterNames);
		$this->parameters[$serializedParameterNames] = $parameter;

		return $parameter;
	}

	/**
	 * @param string[] $parameterNames
	 * @return string[]
	 */
	private function getCzechParameterNamesFromCsvParameterNames($parameterNames) {
		return [
			'cs' => $parameterNames['cs'],
		];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\Parameter $parameter
	 * @param array $parameterNames
	 */
	private function addEnglishTranslationToParameter(Parameter $parameter, array $parameterNames) {
		$parameterData = new ParameterData();
		$parameterData->setFromEntity($parameter);
		$parameterData->name['en'] = $parameterNames['en'];

		$this->parameterFacade->edit($parameter->getId(), $parameterData);
	}

	/**
	 * @param string[] $czechParameterNames
	 * @param string[] $allParameterNames
	 * @return \SS6\ShopBundle\Model\Product\Parameter\Parameter
	 */
	private function findParameterByCzechNamesOrCreateNewByAllNames($czechParameterNames, $allParameterNames) {
		$parameter = $this->parameterFacade->findParameterByNames($czechParameterNames);

		if ($parameter === null) {
			$visible = true;
			$parameterData = new ParameterData($allParameterNames, $visible);
			$parameter = $this->parameterFacade->create($parameterData);
		}

		return $parameter;
	}

}
