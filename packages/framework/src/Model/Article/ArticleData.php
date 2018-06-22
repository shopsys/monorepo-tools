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

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\Article $article
     */
    public function setFromEntity(Article $article)
    {
        $this->name = $article->getName();
        $this->text = $article->getText();
        $this->seoTitle = $article->getSeoTitle();
        $this->seoMetaDescription = $article->getSeoMetaDescription();
        $this->domainId = $article->getDomainId();
        $this->placement = $article->getPlacement();
        $this->hidden = $article->isHidden();
        $this->seoH1 = $article->getSeoH1();
    }
}
