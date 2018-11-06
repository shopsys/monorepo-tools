<?php

namespace Shopsys\FrameworkBundle\Model\Article;

interface ArticleDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Article\ArticleData
     */
    public function create(): ArticleData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\Article $article
     * @return \Shopsys\FrameworkBundle\Model\Article\ArticleData
     */
    public function createFromArticle(Article $article): ArticleData;
}
