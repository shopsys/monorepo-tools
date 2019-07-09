<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

class FriendlyUrlDataFactory implements FriendlyUrlDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData
     */
    public function create(): FriendlyUrlData
    {
        return new FriendlyUrlData();
    }

    /**
     * @param int $id
     * @param string $name
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData
     */
    public function createFromIdAndName(int $id, string $name): FriendlyUrlData
    {
        $friendlyUrlData = $this->create();
        $friendlyUrlData->id = $id;
        $friendlyUrlData->name = $name;

        return $friendlyUrlData;
    }
}
