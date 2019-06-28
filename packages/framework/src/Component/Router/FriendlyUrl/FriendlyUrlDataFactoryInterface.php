<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

/**
 * @method FriendlyUrlData createFromIdAndName(int $id, string $name)
 */
interface FriendlyUrlDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData
     */
    public function create(): FriendlyUrlData;
}
