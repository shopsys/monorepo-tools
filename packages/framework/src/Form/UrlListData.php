<?php

namespace Shopsys\FrameworkBundle\Form;

class UrlListData
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public $toDelete;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
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
     * @see \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade::saveUrlListFormData()
     */
    public $newUrls;

    public function __construct()
    {
        $this->toDelete = [];
        $this->mainFriendlyUrlsByDomainId = [];
        $this->newUrls = [];
    }
}
