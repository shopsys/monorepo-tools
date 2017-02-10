<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl;

use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;

class FriendlyUrlUniqueResult
{

    /**
     * @var bool
     */
    private $unique;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl
     */
    private $friendlyUrlForPersist;

    /**
     * @param bool $unique
     * @param \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null $friendlyUrl
     */
    public function __construct($unique, FriendlyUrl $friendlyUrl = null) {
        $this->unique = $unique;
        $this->friendlyUrlForPersist = $friendlyUrl;
    }

    /**
     * @return bool
     */
    public function isUnique() {
        return $this->unique;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
     */
    public function getFriendlyUrlForPersist() {
        return $this->friendlyUrlForPersist;
    }

}
