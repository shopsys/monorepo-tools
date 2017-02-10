<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Twig_Extension;
use Twig_SimpleFunction;

class InputPriceLabelExtension extends Twig_Extension
{

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    public function __construct(
        PricingSetting $pricingSetting
    ) {
        $this->pricingSetting = $pricingSetting;
    }

    /**
     * @return array
     */
    public function getFunctions() {
        return [
            new Twig_SimpleFunction('inputPriceLabel', [$this, 'getInputPriceLabel']),
        ];
    }

    /**
     * @return string
     */
    public function getInputPriceLabel() {
        $inputPriceType = $this->pricingSetting->getInputPriceType();

        switch ($inputPriceType) {
            case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
                return t('Input price without VAT');

            case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
                return t('Input price with VAT');

            default:
                throw new \Shopsys\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException(
                    'Invalid input price type: ' . $inputPriceType);
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return 'input_price_label_extension';
    }
}
