<?php

namespace Shopsys\ProductFeed\HeurekaDeliveryBundle;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;
use Shopsys\FrameworkBundle\Model\Feed\FeedInterface;
use Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryFeedItemFacade;

class HeurekaDeliveryFeed implements FeedInterface
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaDeliveryBundle\HeurekaDeliveryFeedInfo
     */
    protected $feedInfo;

    /**
     * @var \Shopsys\ProductFeed\HeurekaDeliveryBundle\Model\FeedItem\HeurekaDeliveryFeedItemFacade
     */
    protected $feedItemFacade;

    public function __construct(HeurekaDeliveryFeedInfo $feedInfo, HeurekaDeliveryFeedItemFacade $feedItemFacade)
    {
        $this->feedInfo = $feedInfo;
        $this->feedItemFacade = $feedItemFacade;
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(): FeedInfoInterface
    {
        return $this->feedInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateFilepath(): string
    {
        return '@ShopsysProductFeedHeurekaDelivery/feed.xml.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(DomainConfig $domainConfig, ?int $lastSeekId, int $maxResults): iterable
    {
        yield from $this->feedItemFacade->getItems($domainConfig, $lastSeekId, $maxResults);
    }
}
