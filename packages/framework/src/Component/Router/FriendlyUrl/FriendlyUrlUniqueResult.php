<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

class FriendlyUrlUniqueResult
{
    /**
     * @var bool
     */
    private $unique;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl
     */
    private $friendlyUrlForPersist;

    /**
     * @param bool $unique
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl|null $friendlyUrl
     */
    public function __construct($unique, FriendlyUrl $friendlyUrl = null)
    {
        $this->unique = $unique;
        $this->friendlyUrlForPersist = $friendlyUrl;
    }

    /**
     * @return bool
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
     */
    public function getFriendlyUrlForPersist()
    {
        return $this->friendlyUrlForPersist;
    }
}
