<?php

namespace Shopsys\ShopBundle\Model\Article;

use DateTime;
use Shopsys\FrameworkBundle\Model\Article\Article as BaseArticle;
use Shopsys\FrameworkBundle\Model\Article\ArticleData as BaseArticleData;

class ArticleData extends BaseArticleData
{
    /**
     * @var \DateTime|null
     */
    public $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        parent::__construct();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article $article
     */
    public function setFromEntity(BaseArticle $article)
    {
        $this->createdAt = $article->getCreatedAt() ?? new DateTime();
        parent::setFromEntity($article);
    }
}
