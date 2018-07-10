<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

interface FeedInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface
     */
    public function getInfo(): FeedInfoInterface;

    /**
     * Returns file path to the Twig template of the feed output.
     *
     * Path should be relative to the feed bundle, eg. "@FeedBundleName/feed.xml.twig".
     * Template has to have blocks named "begin", "item" and "end".
     *
     * @return string
     */
    public function getTemplateFilepath(): string;

    /**
     * Returns items that will be passed to the Twig template during feed export.
     * The items' seek IDs must be in ascending order. All items that should be included must be returned, up to $maxResults.
     *
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $lastSeekId
     * @param int $maxResults
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedItemInterface[]|iterable
     */
    public function getItems(DomainConfig $domainConfig, ?int $lastSeekId, int $maxResults): iterable;
}
