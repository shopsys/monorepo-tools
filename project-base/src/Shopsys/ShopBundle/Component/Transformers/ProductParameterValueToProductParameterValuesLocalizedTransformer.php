<?php

namespace SS6\ShopBundle\Component\Transformers;

use SS6\ShopBundle\Model\Product\Parameter\ParameterValueData;
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

		if (!is_array($normData)) {
			throw new \Symfony\Component\Form\Exception\TransformationFailedException('Invalid value');
		}

		$normValue = [];
		foreach ($normData as $productParameterValueData) {
			/* @var $productParameterValueData \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData */
			$parameterId = $productParameterValueData->parameter->getId();
			$locale = $productParameterValueData->parameterValueData->locale;

			if (!array_key_exists($parameterId, $normValue)) {
				$normValue[$parameterId] = new ProductParameterValuesLocalizedData();
				$normValue[$parameterId]->parameter = $productParameterValueData->parameter;
				$normValue[$parameterId]->valueText = [];
			}

			if (array_key_exists($locale, $normValue[$parameterId]->valueText)) {
				throw new \Symfony\Component\Form\Exception\TransformationFailedException('Duplicate parameter');
			}

			$normValue[$parameterId]->valueText[$locale] = $productParameterValueData->parameterValueData->text;
		}

		return array_values($normValue);
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
						$productParameterValueData->parameter = $productParameterValuesLocalizedData->parameter;
						$productParameterValueData->parameterValueData = new ParameterValueData($valueText, $locale);

						$normData[] = $productParameterValueData;
					}
				}
			}

			return $normData;
		}

		throw new \Symfony\Component\Form\Exception\TransformationFailedException('Invalid value');
	}

}
