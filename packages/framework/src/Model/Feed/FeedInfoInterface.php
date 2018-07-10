<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

interface FeedInfoInterface
{
    /**
     * Returns human readable label to identify this product feed.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Returns unique name to identify this product feed.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * May return additional information about the product feed for the administrator.
     *
     * @return string|null
     */
    public function getAdditionalInformation(): ?string;
}
