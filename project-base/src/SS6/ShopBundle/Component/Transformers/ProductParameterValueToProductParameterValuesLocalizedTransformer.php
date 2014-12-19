<?php

namespace SS6\ShopBundle\Component\Transformers;

use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData;
use Symfony\Component\Form\DataTransformerInterface;

class ProductParameterValueToProductParameterValuesLocalizedTransformer implements DataTransformerInterface {

	/**
	 * @param mixed $normData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData[]
	 */
	public function transform($normData) {
		if ($normData === null) {
			return null;
		}

		if (is_array($normData)) {
			$normValue = [];

			foreach ($normData as $productParameterValueData) {
				/* @var $productParameterValueData \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData */
				$parameterId = $productParameterValueData->getParameter()->getId();
				$locale = $productParameterValueData->getLocale();

				if (array_key_exists($parameterId, $normValue)) {
					$productParameterValuesLocalizedData = $normValue[$parameterId];
				} else {
					$productParameterValuesLocalizedData = new ProductParameterValuesLocalizedData();
				}

				$productParameterValuesLocalizedData->parameter = $productParameterValueData->getParameter();
				$productParameterValuesLocalizedData->valueText[$locale] = $productParameterValueData->getValueText();

				$normValue[$parameterId] = $productParameterValuesLocalizedData;
			}

			return $this->resetArrayIndexes($normValue);
		}

		throw new \Symfony\Component\Form\Exception\TransformationFailedException('Invalid value');
	}

	/**
	 * @param mixed $viewData
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	public function reverseTransform($viewData) {
		if (is_array($viewData)) {
			$normData = [];

			foreach ($viewData as $productParameterValuesLocalizedData) {
				/* @var $productParameterValuesLocalizedData \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData */

				foreach ($productParameterValuesLocalizedData->valueText as $locale => $valueText) {
					if ($valueText !== null) {
						$productParameterValueData = new ProductParameterValueData();
						$productParameterValueData->setParameter($productParameterValuesLocalizedData->parameter);
						$productParameterValueData->setLocale($locale);
						$productParameterValueData->setValueText($valueText);

						$normData[] = $productParameterValueData;
					}
				}
			}

			return $normData;
		}

		throw new \Symfony\Component\Form\Exception\TransformationFailedException('Invalid value');
	}

	/**
	 * @param array $array
	 * @return array
	 */
	private function resetArrayIndexes($array) {
		$newArray = [];
		foreach ($array as $item) {
			$newArray[] = $item;
		}

		return $newArray;
	}

}
