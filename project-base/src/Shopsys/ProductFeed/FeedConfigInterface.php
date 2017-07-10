<?php

namespace Shopsys\ProductFeed;

use Shopsys\ProductFeed\FeedItemRepositoryInterface;

interface FeedConfigInterface
{
    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getFeedName();

    /**
     * @return string
     */
    public function getTemplateFilepath();

    /**
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository();
}
