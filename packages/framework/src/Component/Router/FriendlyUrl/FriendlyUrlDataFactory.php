<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

class FriendlyUrlDataFactory
{
    const ARRAY_ID_FIELD = 'id';
    const ARRAY_NAME_FIELD = 'name';

    /**
     * @param $data
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData
     */
    public function createFromData($data)
    {
        if (!$this->validateData($data)) {
            throw new \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\Exception\InvalidFriendlyUrlDataArrayException();
        }

        $friendlyUrlData = new FriendlyUrlData();
        $friendlyUrlData->name = $data[self::ARRAY_NAME_FIELD];
        $friendlyUrlData->id = $data[self::ARRAY_ID_FIELD];

        return $friendlyUrlData;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function validateData($data)
    {
        if (!isset($data[self::ARRAY_ID_FIELD]) || !isset($data[self::ARRAY_ID_FIELD])) {
            return false;
        }

        return true;
    }
}
