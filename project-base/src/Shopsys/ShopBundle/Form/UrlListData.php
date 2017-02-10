<?php

namespace Shopsys\ShopBundle\Form;

class UrlListData
{

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public $toDelete;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[domainId]
     */
    public $mainOnDomains;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public $newUrls;

    public function __construct() {
        $this->toDelete = [];
        $this->mainOnDomains = [];
        $this->newUrls = [];
    }
}
