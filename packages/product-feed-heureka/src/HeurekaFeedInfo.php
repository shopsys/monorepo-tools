<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;

class HeurekaFeedInfo implements FeedInfoInterface
{
    /**
     * @return string
     */
    public function getLabel(): string
    {
        return 'Heureka';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'heureka';
    }

    /**
     * @return string|null
     */
    public function getAdditionalInformation(): ?string
    {
        return null;
    }
}
