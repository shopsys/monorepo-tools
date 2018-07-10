<?php

namespace Shopsys\ProductFeed\HeurekaDeliveryBundle;

use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;

class HeurekaDeliveryFeedInfo implements FeedInfoInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Heureka - delivery';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'heureka_delivery';
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInformation(): ?string
    {
        return null;
    }
}
