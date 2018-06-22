<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFactoryInterface;

class OrderItemPriceCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation
     */
    private $priceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFactoryInterface
     */
    protected $vatFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation $priceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFactoryInterface $vatFactory
     */
    public function __construct(
        PriceCalculation $priceCalculation,
        VatFactoryInterface $vatFactory
    ) {
        $this->priceCalculation = $priceCalculation;
        $this->vatFactory = $vatFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderItemData
     * @return string
     */
    public function calculatePriceWithoutVat(OrderItemData $orderItemData)
    {
        $vatData = new VatData();
        $vatData->name = 'orderItemVat';
        $vatData->percent = $orderItemData->vatPercent;
        $vat = $this->vatFactory->create($vatData);
        $vatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($orderItemData->priceWithVat, $vat);

        return $orderItemData->priceWithVat - $vatAmount;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function calculateTotalPrice(OrderItem $orderItem)
    {
        $vatData = new VatData();
        $vatData->name = 'orderItemVat';
        $vatData->percent = $orderItem->getVatPercent();
        $vat = $this->vatFactory->create($vatData);

        $totalPriceWithVat = $orderItem->getPriceWithVat() * $orderItem->getQuantity();
        $totalVatAmount = $this->priceCalculation->getVatAmountByPriceWithVat($totalPriceWithVat, $vat);
        $totalPriceWithoutVat = $totalPriceWithVat - $totalVatAmount;

        return new Price($totalPriceWithoutVat, $totalPriceWithVat);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem[] $orderItems
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price[]
     */
    public function calculateTotalPricesIndexedById($orderItems)
    {
        $prices = [];

        foreach ($orderItems as $orderItem) {
            if ($orderItem->getId() === null) {
                $message = 'OrderItem must have ID filled';
                throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\OrderItemHasNoIdException($message);
            }
            $prices[$orderItem->getId()] = $this->calculateTotalPrice($orderItem);
        }

        return $prices;
    }
}
