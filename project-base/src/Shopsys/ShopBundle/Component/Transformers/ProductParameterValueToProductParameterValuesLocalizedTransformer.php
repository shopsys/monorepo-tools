<?php

namespace Shopsys\FrameworkBundle\Component\Transformers;

use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValueData;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData;
use Symfony\Component\Form\DataTransformerInterface;

class ProductParameterValueToProductParameterValuesLocalizedTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $normData
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData[]
     */
    public function transform($normData)
    {
        if ($normData === null) {
            return null;
        }

        if (!is_array($normData)) {
            throw new \Symfony\Component\Form\Exception\TransformationFailedException('Invalid value');
        }

        $normValue = [];
        foreach ($normData as $productParameterValueData) {
            /* @var $productParameterValueData \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData */
            $parameterId = $productParameterValueData->parameter->getId();
            $locale = $productParameterValueData->parameterValueData->locale;

            if (!array_key_exists($parameterId, $normValue)) {
                $normValue[$parameterId] = new ProductParameterValuesLocalizedData();
                $normValue[$parameterId]->parameter = $productParameterValueData->parameter;
                $normValue[$parameterId]->valueTextsByLocale = [];
            }

            if (array_key_exists($locale, $normValue[$parameterId]->valueTextsByLocale)) {
                throw new \Symfony\Component\Form\Exception\TransformationFailedException('Duplicate parameter');
            }

            $normValue[$parameterId]->valueTextsByLocale[$locale] = $productParameterValueData->parameterValueData->text;
        }

        return array_values($normValue);
    }

    /**
     * @param mixed $viewData
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    public function reverseTransform($viewData)
    {
        if (is_array($viewData)) {
            $normData = [];

            foreach ($viewData as $productParameterValuesLocalizedData) {
                /* @var $productParameterValuesLocalizedData \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData */

                foreach ($productParameterValuesLocalizedData->valueTextsByLocale as $locale => $valueText) {
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
