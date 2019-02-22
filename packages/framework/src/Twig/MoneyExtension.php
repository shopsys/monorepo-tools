<?php

namespace Shopsys\FrameworkBundle\Twig;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Twig_Extension;
use Twig_SimpleFilter;

class MoneyExtension extends Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter(
                'moneyFormat',
                [$this, 'moneyFormatFilter']
            ),
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $money
     * @param int|null $decimal
     * @param string $decimalPoint
     * @param string $thousandsSeparator
     * @return string
     */
    public function moneyFormatFilter(Money $money, int $decimal = null, string $decimalPoint = '.', string $thousandsSeparator = '')
    {
        $moneyString = $money->getAmount();

        if ($decimal === null) {
            $decimal = $this->getNumberOfDecimalPlaces($moneyString);
        }

        return number_format($moneyString, $decimal, $decimalPoint, $thousandsSeparator);
    }

    /**
     * @param string $numeric
     * @return int
     */
    protected function getNumberOfDecimalPlaces(string $numeric): int
    {
        $decimalPointPosition = strpos($numeric, '.');

        if ($decimalPointPosition === false) {
            return 0;
        }

        return strlen($numeric) - $decimalPointPosition - 1;
    }
}
