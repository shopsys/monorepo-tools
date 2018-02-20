<?php

namespace Shopsys\ProductFeed;

/**
 * Interface of a product feed plugin.
 *
 * Implementations should be tagged in a DI container with "shopsys.product_feed" tag.
 */
interface FeedConfigInterface
{
    /**
     * Returns human readable label to identify this product feed.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns unique name to identify this product feed.
     *
     * @return string
     */
    public function getFeedName();

    /**
     * Returns file path to the Twig template of the feed output.
     *
     * Path should be relative to the bundle, eg. "@FeedBundleName/feed.xml.twig".
     * Template has to have blocks named "begin", "item" and "end".
     *
     * @return string
     */
    public function getTemplateFilepath();

    /**
     * Returns additional information about the product feed for the administrator.
     *
     * @return string|null
     */
    public function getAdditionalInformation();

    /**
     * Filters or modifies a batch of feed items before passing it into the template.
     *
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig);
}
