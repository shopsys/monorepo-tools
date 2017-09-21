<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;

class OrderItemPriceCalculation
{
    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PriceCalculation
     */
    private $priceCalculation;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\PriceCalculation $priceCalculation
     */
    public function __construct(PriceCalculation $priceCalculation)
    {
        $this->priceCalculation = $priceCalculation;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItemData $orderItemData
     * @return string
     */
    public function calculatePriceWithoutVat(OrderItemData $orderItemData)
    {
        $vat = new Vat(new VatData('orderItemVat', $orderItemData->vatPercent));
        $vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($orderItemData->priceWithVat, $vat);

        return $orderItemData->priceWithVat - $vatAmount;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItem $orderItem
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function calculateTotalPrice(OrderItem $orderItem)
    {
        $vat = new Vat(new VatData('orderItemVat', $orderItem->getVatPercent()));

        $totalPriceWithVat = $orderItem->getPriceWithVat() * $orderItem->getQuantity();
        $totalVatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($totalPriceWithVat, $vat);
        $totalPriceWithoutVat = $totalPriceWithVat - $totalVatAmount;

        return new Price($totalPriceWithoutVat, $totalPriceWithVat);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItem[] $orderItems
     * @return \Shopsys\ShopBundle\Model\Pricing\Price[]
     */
    public function calculateTotalPricesIndexedById($orderItems)
    {
        $prices = [];

        foreach ($orderItems as $orderItem) {
            if ($orderItem->getId() === null) {
                $message = 'OrderItem must have ID filled';
                throw new \Shopsys\ShopBundle\Model\Order\Item\Exception\OrderItemHasNoIdException($message);
            }
            $prices[$orderItem->getId()] = $this->calculateTotalPrice($orderItem);
        }

        return $prices;
    }
}
