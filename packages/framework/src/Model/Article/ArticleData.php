<?php

namespace Shopsys\FrameworkBundle\Model\Article;

use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData;

class ArticleData
{
    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $text;

    /**
     * @var string|null
     */
    public $seoTitle;

    /**
     * @var string|null
     */
    public $seoMetaDescription;

    /**
     * @var int|null
     */
    public $domainId;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData
     */
    public $urls;

    /**
     * @var string|null
     */
    public $placement;

    /**
     * @var bool
     */
    public $hidden;

    /**
     * @var string|null
     */
    public $seoH1;

    public function __construct()
    {
        $this->urls = new UrlListData();
        $this->hidden = false;
    }
}
