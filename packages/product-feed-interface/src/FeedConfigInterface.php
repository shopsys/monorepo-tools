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
     * Returns feed item repository that is to be used.
     *
     * If you do not need to load feed items with a specific repository, you can just return an implementation
     * injected in your constructor. Just type hint a constructor argument with the interface and an implementation
     * will by provided by autowiring or manual choice of a service by the user.
     *
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository();

    /**
     * Filters or modifies a batch of feed items before passing it into the template.
     *
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig);
}
