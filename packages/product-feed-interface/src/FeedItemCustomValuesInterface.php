<?php

namespace Shopsys\ProductFeed;

interface FeedItemCustomValuesInterface
{
    /**
     * @return string|null
     */
    public function getHeurekaCpc();

    /**
     * @return bool
     */
    public function getShowInZboziFeed();

    /**
     * @return string|null
     */
    public function getZboziCpc();

    /**
     * @return string|null
     */
    public function getZboziCpcSearch();
}
