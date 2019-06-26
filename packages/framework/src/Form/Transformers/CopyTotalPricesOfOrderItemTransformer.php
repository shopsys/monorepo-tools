<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Transformers;

use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class CopyTotalPricesOfOrderItemTransformer implements DataTransformerInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData|null $value
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData|null
     */
    public function transform($value): ?OrderItemData
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof OrderItemData) {
            throw new TransformationFailedException(sprintf('Instance of %s or null must be provided.', OrderItemData::class));
        }

        if ($value->quantity !== 1) {
            throw new TransformationFailedException(sprintf('A single item must provided for total prices to be copiable, quantity of %d provided.', $value->quantity));
        }

        $value->totalPriceWithVat = $value->priceWithVat;
        $value->totalPriceWithoutVat = $value->priceWithoutVat;

        return $value;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData|null $value
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData|null
     */
    public function reverseTransform($value): ?OrderItemData
    {
        return $this->transform($value);
    }
}
