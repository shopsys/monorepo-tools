<?php

namespace Shopsys\ShopBundle\Model\Article;

use Shopsys\FrameworkBundle\Model\Article\Article as BaseArticle;
use Shopsys\FrameworkBundle\Model\Article\ArticleData as BaseArticleData;
use Shopsys\FrameworkBundle\Model\Article\ArticleFactoryInterface;

class ArticleFactory implements ArticleFactoryInterface
{
    /**
     * @param \Shopsys\ShopBundle\Model\Article\ArticleData $data
     * @return \Shopsys\ShopBundle\Model\Article\Article
     */
    public function create(BaseArticleData $data): BaseArticle
    {
        return new Article($data);
    }
}
