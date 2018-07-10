<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface;
use Shopsys\FrameworkBundle\Model\Feed\FeedInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItemFacade;

class HeurekaFeed implements FeedInterface
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\HeurekaFeedInfo
     */
    protected $feedInfo;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem\HeurekaFeedItemFacade
     */
    protected $feedItemFacade;

    public function __construct(HeurekaFeedInfo $feedInfo, HeurekaFeedItemFacade $feedItemFacade)
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
        return '@ShopsysProductFeedHeureka/feed.xml.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(DomainConfig $domainConfig, ?int $lastSeekId, int $maxResults): iterable
    {
        yield from $this->feedItemFacade->getItems($domainConfig, $lastSeekId, $maxResults);
    }
}
