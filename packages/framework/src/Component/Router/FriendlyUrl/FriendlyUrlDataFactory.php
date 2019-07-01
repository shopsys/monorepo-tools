<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

class FriendlyUrlDataFactory implements FriendlyUrlDataFactoryInterface
{
    /**
     * @deprecated since Shopsys Framework 7.3, use createFromIdAndName instead
     *
     * @param array $data
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData
     */
    public function createFromData($data)
    {
        @trigger_error(
            sprintf(
                'Using "%s" is deprecated since Shopsys Framework 7.3, use "%s" instead',
                self::class . '::createFromData()',
                self::class . '::createFromIdAndName()'
            ),
            E_USER_DEPRECATED
        );
        $friendlyUrlData = new FriendlyUrlData();
        $friendlyUrlData->id = $data['id'];
        $friendlyUrlData->name = $data['name'];

        return $friendlyUrlData;
    }

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
