<?php

namespace Shopsys\ShopBundle\Model\Article;

use Shopsys\FrameworkBundle\Model\Article\ArticleData as BaseArticleData;

class ArticleData extends BaseArticleData
{
    /**
     * @var \DateTime|null
     */
    public $createdAt;
}
