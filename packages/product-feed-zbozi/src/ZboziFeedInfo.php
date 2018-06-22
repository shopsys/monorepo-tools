<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;

class ZboziFeedInfo implements FeedInfoInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Zboží.cz';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'zbozi';
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalInformation(): ?string
    {
        return null;
    }
}
