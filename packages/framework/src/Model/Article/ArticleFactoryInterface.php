<?php

namespace Shopsys\FrameworkBundle\Model\Article;

interface ArticleFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleData $data
     * @return \Shopsys\FrameworkBundle\Model\Article\Article
     */
    public function create(ArticleData $data): Article;
}
