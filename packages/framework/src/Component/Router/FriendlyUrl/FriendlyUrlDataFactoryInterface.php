<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

interface FriendlyUrlDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData
     */
    public function create(): FriendlyUrlData;
}
