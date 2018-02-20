<?php

namespace Shopsys\ProductFeed;

interface DomainConfigInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getLocale();
}
