<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

class UrlListData
{
    public const FIELD_DOMAIN = 'domain';
    public const FIELD_SLUG = 'slug';

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl[][]
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
