<?php

namespace Shopsys\ShopBundle\Form;

class UrlListData
{
    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public $toDelete;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public $mainFriendlyUrlsByDomainId;

    /**
     * @var array[]
     *
     * Format:
     * [
     *     [
     *         'domain' => 1,
     *         'slug' => 'slug-for-the-first-domain',
     *     ],
     *     ...
     * ]
     *
     * @see \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade::saveUrlListFormData()
     */
    public $newUrls;

    public function __construct()
    {
        $this->toDelete = [];
        $this->mainFriendlyUrlsByDomainId = [];
        $this->newUrls = [];
    }
}
